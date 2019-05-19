<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 15.4.19
 * Time: 11:18
 */

namespace App\FrontModule\Presenters;

use App\Components\Forms\ProblemFilterFormFactory;
use App\Model\Repository\CategoryRepository;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemFinalRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Service\Authorizator;

use IPub\VisualPaginator\Components as VisualPaginator;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class CategoryPresenter
 * @package App\FrontModule\Presenters
 */
class CategoryPresenter extends FrontPresenter
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var SubCategoryRepository
     */
    protected $subCategoryRepository;

    /**
     * @var DifficultyRepository
     */
    protected $difficultyRepository;

    /**
     * @var ProblemFinalRepository
     */
    protected $problemFinalRepository;

    /**
     * @var ProblemFilterFormFactory
     */
    protected $problemFilterFormFactory;

    /**
     * @persistent
     */
    public $filters = [];

    /**
     * @persistent
     */
    public $id;

    /**
     * CategoryPresenter constructor.
     * @param Authorizator $authorizator
     * @param CategoryRepository $categoryRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemFinalRepository $problemFinalRepository
     * @param ProblemFilterFormFactory $problemFilterFormFactory
     */
    public function __construct
    (
        Authorizator $authorizator,
        CategoryRepository $categoryRepository, SubCategoryRepository $subCategoryRepository, DifficultyRepository $difficultyRepository,
        ProblemFinalRepository $problemFinalRepository,
        ProblemFilterFormFactory $problemFilterFormFactory
    )
    {
        parent::__construct($authorizator);
        $this->categoryRepository = $categoryRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->difficultyRepository = $difficultyRepository;
        $this->problemFinalRepository = $problemFinalRepository;
        $this->problemFilterFormFactory = $problemFilterFormFactory;
    }

    /**
     * @param int $id
     * @param bool $clear_filters
     * @param int $page
     * @param array|null $filters
     * @throws \Nette\Application\AbortException
     */
    public function actionDefault(int $id, bool $clear_filters = false, int $page = 1, array $filters = null): void
    {
        if(!$this->authorizator->isCategoryAllowed($this->user->identity, $id)){
            $this->flashMessage("Nedostatečná přístupová práva.", "danger");
            $this->redirect("Homepage:default");
        }

        if($filters)
            $this->filters = $filters;

        if($clear_filters)
            $this->clearFilters();

        $this->setFilters();

        $this->template->filters = $this->filters;
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function renderDefault(int $id): void
    {
        $category = $this->categoryRepository->find($id);
        $this->template->id = $id;
        $this->template->label = $category->getLabel();

        $problemsCnt = $this->problemFinalRepository->getFilteredCnt($id, $this->filters);

        $visualPaginator = $this["visualPaginator"];
        $paginator = $visualPaginator->getPaginator();
        $paginator->itemsPerPage = 1;
        $paginator->itemCount = $problemsCnt;

        $problems = $this->problemFinalRepository->getFiltered($id, $paginator->itemsPerPage, $paginator->offset, $this->filters);

        $this->template->problems = $problems;
        $this->template->paginator = $paginator;
        $this->template->difficulties = $this->difficultyRepository->findAssoc([], "id");

        $this->id = $id;

        $this->redrawControl("paginatorSnippet");
        $this->redrawControl("problemsSnippet");
        $this->redrawControl("filtersSnippet");
    }

    public function clearFilters()
    {
        $this->filters = [];
        $this["problemFilterForm"]["difficulty"]->setDefaultValue([]);
    }

    /**
     * @param ArrayHash|null $filters
     */
    public function setFilters(ArrayHash $filters = null)
    {
        //Set filters values to the filter form
        if($filters === null){
            foreach($this->filters as $filterKey => $filter)
                $this["problemFilterForm"][$filterKey]->setDefaultValue($filter);
            return;
        }

        //Reset filters
        $this->clearFilters();

        //Set new filters
        foreach($filters as $filterKey => $filter)
            if( (is_array($filter) && count($filter) > 0) || !is_array($filter) )
                $this->filters[$filterKey] = $filter;
    }

    /**
     * @return VisualPaginator\Control
     */
    public function createComponentVisualPaginator()
    {
        $paginator = new VisualPaginator\Control;
        $paginator->enableAjax();
        $paginator->setTemplateFile(__DIR__ . "/../../presenters/templates/VisualPaginator/bootstrap.latte");
        $paginator->onShowPage[] = function() {
            $this->redrawControl("paginatorSnippet");
            $this->redrawControl("problemsSnippet");
            $this->redrawControl("mathJaxRender");
        };
        return $paginator;
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentProblemFilterForm()
    {
        $form = $this->problemFilterFormFactory->create();
        $themeOptions = $this->subCategoryRepository->findAssoc(["category" => $this->id], "id");
        $form->addMultiSelect("theme", "Témata", $themeOptions)
            ->setHtmlAttribute("class", "form-control selectpicker");
        $form->onSuccess[] = [$this, "handleFilterFormSuccess"];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Nette\Application\AbortException
     */
    public function handleFilterFormSuccess(Form $form, ArrayHash $values)
    {
        bdump($values);
        $this->setFilters($values);
        $this->redirect("Category:default", $this->id, false, 1,  $this->filters);
    }

    public function handleClearFilters()
    {
        $this->clearFilters();
        bdump($this->filters);
        //$this->setFilters();
        $this->redirect("Category:default", $this->id, false, 1,  $this->filters);
    }
}
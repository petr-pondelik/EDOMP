<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 15.4.19
 * Time: 11:18
 */

namespace App\FrontModule\Presenters;

use App\Components\Forms\ProblemFilterFormFactory;
use App\Model\Managers\CategoryManager;
use App\Model\Managers\DifficultyManager;
use App\Model\Managers\SubCategoryManager;
use App\Services\Authorizator;

use IPub\VisualPaginator\Components as VisualPaginator;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Nette\Utils\Paginator;

/**
 * Class CategoryPresenter
 * @package App\FrontModule\Presenters
 */
class CategoryPresenter extends FrontPresenter
{
    /**
     * @var CategoryManager
     */
    protected $categoryManager;

    /**
     * @var SubCategoryManager
     */
    protected $subCategoryManager;

    /**
     * @var DifficultyManager
     */
    protected $difficultyManager;

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
    public $category_id;

    /**
     * CategoryPresenter constructor.
     * @param Authorizator $authorizator
     * @param CategoryManager $categoryManager
     * @param SubCategoryManager $subCategoryManager
     * @param DifficultyManager $difficultyManager
     * @param ProblemFilterFormFactory $problemFilterFormFactory
     */
    public function __construct
    (
        Authorizator $authorizator,
        CategoryManager $categoryManager, SubCategoryManager $subCategoryManager, DifficultyManager $difficultyManager,
        ProblemFilterFormFactory $problemFilterFormFactory
    )
    {
        parent::__construct($authorizator);
        $this->categoryManager = $categoryManager;
        $this->subCategoryManager = $subCategoryManager;
        $this->difficultyManager = $difficultyManager;
        $this->problemFilterFormFactory = $problemFilterFormFactory;
    }

    /**
     * @param int $category_id
     * @param bool $clear_filters
     * @param int $page
     * @param array|null $filters
     * @throws \Nette\Application\AbortException
     */
    public function actionDefault(int $category_id, bool $clear_filters = false, int $page = 1, array $filters = null): void
    {
        if(!$this->authorizator->isCategoryAllowed($this->user->identity, $category_id)){
            $this->flashMessage("Nemáte oprávnění k přístupu.", "danger");
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
     * @param int $category_id
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function renderDefault(int $category_id): void
    {
        $category = $this->categoryManager->getById($category_id);
        $this->template->categoryId = $category_id;
        $this->template->categoryLabel = $category->label;

        $problemsCnt = $this->categoryManager->getProblemsFilteredCnt($category_id, $this->filters);

        $visualPaginator = $this["visualPaginator"];
        $paginator = $visualPaginator->getPaginator();
        $paginator->itemsPerPage = 1;
        $paginator->itemCount = $problemsCnt;

        $problems = $this->categoryManager->getProblemsFiltered($category_id, $paginator->itemsPerPage, $paginator->offset, $this->filters);

        $this->template->problems = $problems;
        $this->template->paginator = $paginator;
        $this->template->difficulties = $this->difficultyManager->getAll("ASC");

        $this->category_id = $category_id;

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
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function createComponentProblemFilterForm()
    {
        $form = $this->problemFilterFormFactory->create();
        $themeOptions = $this->subCategoryManager->getByCond("category_id = " . $this->category_id);
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
        $this->redirect("Category:default", $this->category_id, false, 1,  $this->filters);
    }

    public function handleClearFilters()
    {
        $this->clearFilters();
        bdump($this->filters);
        //$this->setFilters();
        $this->redirect("Category:default", $this->category_id, false, 1,  $this->filters);
    }
}
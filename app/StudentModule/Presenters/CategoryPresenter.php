<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 15.4.19
 * Time: 11:18
 */

namespace App\StudentModule\Presenters;

use App\StudentModule\Components\Forms\ProblemFilterForm\ProblemFilterFormControl;
use App\StudentModule\Components\Forms\ProblemFilterForm\IProblemFilterFormFactory;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Model\Persistent\Repository\ThemeRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemFinal\ProblemFinalRepository;
use App\CoreModule\Services\Authorizator;
use IPub\VisualPaginator\Components as VisualPaginator;
use Nette\Utils\ArrayHash;

/**
 * Class ThemePresenter
 * @package App\StudentModule\Presenters
 */
class ThemePresenter extends StudentPresenter
{
    /**
     * @var ThemeRepository
     */
    protected $themeRepository;

    /**
     * @var ProblemFinalRepository
     */
    protected $problemFinalRepository;

    /**
     * @var IProblemFilterFormFactory
     */
    protected $problemFilterFormFactory;

    /**
     * @persistent
     */
    public $filters = [];

    /**
     * @persistent
     */
    public $page;

    /**
     * @persistent
     */
    public $id;

    /**
     * ThemePresenter constructor.
     * @param Authorizator $authorizator
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param ThemeRepository $themeRepository
     * @param ProblemFinalRepository $problemFinalRepository
     * @param IProblemFilterFormFactory $problemFilterFormFactory
     */
    public function __construct
    (
        Authorizator $authorizator,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        ThemeRepository $themeRepository, ProblemFinalRepository $problemFinalRepository,
        IProblemFilterFormFactory $problemFilterFormFactory
    )
    {
        parent::__construct($authorizator, $headerBarFactory, $sideBarFactory, $flashesTranslator);
        $this->themeRepository = $themeRepository;
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
        if(!$this->authorizator->isThemeAllowed($this->user->identity, $id)){
            $this->flashMessage('Nedostatečná přístupová práva.', 'danger');
            $this->redirect('Homepage:default');
        }
        if($filters){
            $this->filters = $filters;
        }
        if($clear_filters){
            $this->clearFilters();
        }
        $this->setFilters();
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function renderDefault(int $id): void
    {
        $theme = $this->themeRepository->find($id);

        if (!$theme) {
            bdump('NOT FOUND');
        }

        $this->template->label = $theme->getLabel();

        $problemsCnt = $this->problemFinalRepository->getStudentFilteredCnt($id, $this->filters);

        $visualPaginator = $this['visualPaginator'];
        $paginator = $visualPaginator->getPaginator();
        $paginator->itemsPerPage = 1;
        $paginator->itemCount = $problemsCnt;

        $problems = $this->problemFinalRepository->getStudentFiltered($id, $paginator->itemsPerPage, $paginator->offset, $this->filters);

        $this->template->problems = $problems;
        $this->template->paginator = $paginator;

        $this->id = $id;

        $this->redrawControl('paginatorSnippet');
        $this->redrawControl('problemsSnippet');
        $this->redrawControl('filtersSnippet');
    }

    public function clearFilters(): void
    {
        $this->filters = [];
        $this['problemFilterForm']['form']['difficulty']->setDefaultValue([]);
    }

    /**
     * @param ArrayHash|null $filters
     */
    public function setFilters(ArrayHash $filters = null): void
    {
        //Set filters values to the filter form
        if($filters === null){
            foreach($this->filters as $filterKey => $filter){
                $this['problemFilterForm']['form'][$filterKey]->setDefaultValue($filter);
            }
            return;
        }

        $this->clearFilters();

        //Set new filters
        foreach($filters as $filterKey => $filter){
            if( (is_array($filter) && count($filter) > 0) || !is_array($filter) ){
                $this->filters[$filterKey] = $filter;
            }
        }
    }

    /**
     * @return VisualPaginator\Control
     */
    public function createComponentVisualPaginator(): VisualPaginator\Control
    {
        $paginator = new VisualPaginator\Control;
        $paginator->enableAjax();
        $paginator->setTemplateFile(STUDENT_MODULE_TEMPLATES_DIR . '/VisualPaginator/frontProblemCollection.latte');
        $paginator->onShowPage[] = function() {
            $this->redrawControl('paginatorSnippet');
            $this->redrawControl('problemsSnippet');
            $this->redrawControl('mathJaxRender');
        };
        return $paginator;
    }

    /**
     * @return ProblemFilterFormControl
     */
    public function createComponentProblemFilterForm(): ProblemFilterFormControl
    {
        $control = $this->problemFilterFormFactory->create($this->getParameter('id'));
        $control->onSuccess[] = function (){
            $this->redirect('Theme:default', $this->id, false, 1,  $this->filters);
        };
        return $control;
    }

    /**
     * @throws \Nette\Application\AbortException
     */
    public function actionClearFilters(): void
    {
        $this->clearFilters();
        $this->redirect('Theme:default', $this->id, false, 1,  $this->filters);
    }
}
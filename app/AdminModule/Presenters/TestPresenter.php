<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 2.3.19
 * Time: 14:48
 */

namespace App\AdminModule\Presenters;

use App\Arguments\UserInformArgs;
use App\Components\DataGrids\TestGridFactory;
use App\Components\Forms\TestForm\TestFormControl;
use App\Components\Forms\TestForm\TestFormFactory;
use App\Components\Forms\TestStatisticsForm\TestStatisticsFormControl;
use App\Components\Forms\TestStatisticsForm\TestStatisticsFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\FlashesTranslator;
use App\Model\Functionality\TestFunctionality;
use App\Model\Repository\LogoRepository;
use App\Model\Repository\ProblemRepository;
use App\Model\Repository\ProblemTemplateRepository;
use App\Model\Repository\ProblemTestAssociationRepository;
use App\Model\Repository\TestRepository;
use App\Services\Authorizator;
use App\Services\FileService;
use App\Services\NewtonApiClient;
use App\Services\ValidationService;
use Nette\Application\Responses\CallbackResponse;
use Nette\ComponentModel\IComponent;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Http\Response;

/**
 * Class TestPresenter
 * @package app\presenters
 */
class TestPresenter extends AdminPresenter
{
    /**
     * @var TestRepository
     */
    protected $testRepository;

    /**
     * @var TestFunctionality
     */
    protected $testFunctionality;

    /**
     * @var ProblemTemplateRepository
     */
    protected $problemTemplateRepository;

    /**
     * @var ProblemRepository
     */
    protected $problemRepository;

    /**
     * @var LogoRepository
     */
    protected $logoRepository;

    /**
     * @var ProblemTestAssociationRepository
     */
    protected $problemTestAssociationRepository;

    /**
     * @var TestFormFactory
     */
    protected $testFormFactory;

    /**
     * @var TestStatisticsFormFactory
     */
    protected $testStatisticsFormFactory;

    /**
     * @var TestGridFactory
     */
    protected $testGridFactory;

    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * @var ValidationService
     */
    protected $validationService;

    /**
     * TestPresenter constructor.
     * @param Authorizator $authorizator
     * @param NewtonApiClient $newtonApiClient
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param TestRepository $testRepository
     * @param TestFunctionality $testFunctionality
     * @param ProblemTemplateRepository $problemTemplateRepository
     * @param ProblemRepository $problemRepository
     * @param LogoRepository $logoRepository
     * @param ProblemTestAssociationRepository $problemTestAssociationRepository
     * @param TestFormFactory $testFormFactory
     * @param TestStatisticsFormFactory $testStatisticsFormFactory
     * @param TestGridFactory $testGridFactory
     * @param FileService $fileService
     * @param ValidationService $validationService
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        TestRepository $testRepository, TestFunctionality $testFunctionality,
        ProblemTemplateRepository $problemTemplateRepository, ProblemRepository $problemRepository, LogoRepository $logoRepository,
        ProblemTestAssociationRepository $problemTestAssociationRepository,
        TestFormFactory $testFormFactory, TestStatisticsFormFactory $testStatisticsFormFactory, TestGridFactory $testGridFactory,
        FileService $fileService, ValidationService $validationService
    )
    {
        parent::__construct($authorizator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator);
        $this->testRepository = $testRepository;
        $this->testFunctionality = $testFunctionality;
        $this->problemTemplateRepository = $problemTemplateRepository;
        $this->problemRepository = $problemRepository;
        $this->logoRepository = $logoRepository;
        $this->problemTestAssociationRepository = $problemTestAssociationRepository;
        $this->testFormFactory = $testFormFactory;
        $this->testStatisticsFormFactory = $testStatisticsFormFactory;
        $this->testGridFactory = $testGridFactory;
        $this->fileService = $fileService;
        $this->validationService = $validationService;
    }

    /**
     * @param int $id
     */
    public function actionStatistics(int $id)
    {
        $form = $this['testStatisticsForm']['form'];
        if(!$form->isSubmitted()){
            $this->template->id = $id;
            $problemAssociations = $this->problemTestAssociationRepository->findBy(["test" => $id]);
            $this->setDefaults($form, $id, $problemAssociations);
        }
    }

    /**
     * @param IComponent $form
     * @param int $testId
     * @param array $problemAssociations
     */
    public function setDefaults(IComponent $form, int $testId, array $problemAssociations)
    {
        $i = 0;
        $form->setDefaults([
            'problems_cnt' => count($problemAssociations),
            'test_id' => $testId
        ]);
        foreach($problemAssociations as $association){
            $form->setDefaults([
                'problem_final_id_disabled_' . $i => $association->getProblem()->getId(),
                'problem_final_id_' . $i => $association->getProblem()->getId(),
                'success_rate_' . $i => $association->getSuccessRate()
            ]);
            if($association->getProblemTemplate()){
                $form->setDefaults([
                    'problem_prototype_id_disabled_' . $i => $association->getProblemTemplate()->getId(),
                    'problem_prototype_id_' . $i => $association->getProblemTemplate()->getId()
                ]);
            }
            $i++;
        }
    }

    /**
     * @param array $filters
     * @throws \Exception
     */
    public function handleFilterChange(array $filters): void
    {
        $this['testCreateForm']->handleFilterChange($filters);
    }

    /**
     * @param $name
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentTestGrid($name)
    {
        $grid = $this->testGridFactory->create($this, $name);
        $grid->addAction('delete', '', 'delete!')
            ->setIcon('trash')
            ->setClass('btn btn-danger btn-sm ajax');
        $grid->addAction('downloadSource', '', 'downloadSource!', ['id'])
            ->setIcon('download')
            ->setClass('btn btn-primary btn-sm');
        $grid->addAction('pdf', '', 'pdfOverleaf!', ['id'])
            ->setIcon('file-pdf')
            ->setClass('btn btn-primary btn-sm')
            ->addAttributes([
                'target' => '_blank'
            ]);
        $grid->addAction("statistics", "", "statistics!")
            ->setIcon("percent")
            ->setClass("btn btn-primary btn-sm")
            ->setTitle("Statistika úspěšnosti");
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function handleDelete(int $id)
    {
        //TODO: INFORM USER
        $this->testFunctionality->delete($id);
        $this["testGrid"]->reload();
        $this->flashMessage("Test úspěšně odstraněn.", "success");
        $this->redrawControl("mainFlashesSnippet");
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handlePdfOverleaf(int $id)
    {
        $this->fileService->moveTestDirToPublic($id);
        $this->redirectUrl('https://www.overleaf.com/docs?snip_uri=http://wiedzmin.4fan.cz/data_public/tests/test_' . $id . '.zip');
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleDownloadSource(int $id)
    {
        $this->sendResponse(new CallbackResponse(function (IRequest $request, IResponse $response) use ($id) {
            $response = new Response();
            $response->setHeader('Content-type', 'application/zip');
            $response->setHeader(
                'Content-Disposition',
                'attachment; filename=test_' . $id . '.zip'
            );
            $response->setHeader(
                "Content-length",
                filesize(DATA_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . 'test_' . $id . '.zip')
            );
            $response->setHeader("Pragma", 'no-cache');
            $response->setHeader("Expires", '0');

            readfile(DATA_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . 'test_' . $id . '.zip');

        }));
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleStatistics(int $id)
    {
        $this->redirect("statistics", $id);
    }

    /**
     * @return TestFormControl
     */
    public function createComponentTestCreateForm(): TestFormControl
    {
        $control =  $this->testFormFactory->create();
        $control->onSuccess[] = function (){
            $this->informUser(new UserInformArgs('create'));
            //$this->redirect('default');
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs('create', true, 'error', $e, false, 'testCreateForm'));
            //$this->redirect('default');
        };
        return $control;
    }

    /**
     * @return TestStatisticsFormControl
     */
    public function createComponentTestStatisticsForm(): TestStatisticsFormControl
    {
        $control = $this->testStatisticsFormFactory->create($this->getParameter('id'));
        $control->onSuccess[] = function (){
            $this->informUser(
                new UserInformArgs(
                    'statistics', true, 'success', null, false, 'testStatisticsForm'
                )
            );
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs('statistics', true, 'error', $e, false, 'testStatisticsForm'));
        };
        return $control;
    }
}
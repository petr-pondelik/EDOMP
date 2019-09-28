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
use App\Components\Forms\EntityFormControl;
use App\Components\Forms\TestForm\TestEntityForm\ITestEntityFormFactory;
use App\Components\Forms\TestStatisticsForm\ITestStatisticsIFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\ISideBarFactory;
use App\Helpers\FlashesTranslator;
use App\Model\Persistent\Functionality\TestFunctionality;
use App\Model\Persistent\Repository\LogoRepository;
use App\Model\Persistent\Repository\ProblemRepository;
use App\Model\Persistent\Repository\ProblemTemplate\ProblemTemplateRepository;
use App\Model\Persistent\Repository\ProblemFinalTestVariantAssociationRepository;
use App\Model\Persistent\Repository\TestRepository;
use App\Services\Authorizator;
use App\Services\FileService;
use App\Services\NewtonApiClient;
use App\Services\TestGeneratorService;
use App\Services\Validator;
use Nette\Application\Responses\CallbackResponse;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Http\Response;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class TestPresenter
 * @package app\presenters
 */
class TestPresenter extends EntityPresenter
{
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
     * @var ProblemFinalTestVariantAssociationRepository
     */
    protected $problemTestAssociationRepository;

    /**
     * @var ITestStatisticsIFormFactory
     */
    protected $testStatisticsFormFactory;

    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var TestGeneratorService
     */
    protected $testGeneratorService;

    /**
     * TestPresenter constructor.
     * @param Authorizator $authorizator
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param HeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param TestRepository $testRepository
     * @param TestFunctionality $testFunctionality
     * @param ProblemTemplateRepository $problemTemplateRepository
     * @param ProblemRepository $problemRepository
     * @param LogoRepository $logoRepository
     * @param ProblemFinalTestVariantAssociationRepository $problemTestAssociationRepository
     * @param ITestEntityFormFactory $testCreateFormFactory
     * @param ITestStatisticsIFormFactory $testStatisticsFormFactory
     * @param TestGridFactory $testGridFactory
     * @param FileService $fileService
     * @param TestGeneratorService $testGeneratorService
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, Validator $validator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        TestRepository $testRepository, TestFunctionality $testFunctionality,
        ProblemTemplateRepository $problemTemplateRepository, ProblemRepository $problemRepository, LogoRepository $logoRepository,
        ProblemFinalTestVariantAssociationRepository $problemTestAssociationRepository,
        ITestEntityFormFactory $testCreateFormFactory, ITestStatisticsIFormFactory $testStatisticsFormFactory, TestGridFactory $testGridFactory,
        FileService $fileService,
        TestGeneratorService $testGeneratorService,
        ISectionHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct(
            $authorizator, $validator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory,
            $testRepository, $testFunctionality, $testGridFactory, $testCreateFormFactory
        );
        $this->problemTemplateRepository = $problemTemplateRepository;
        $this->problemRepository = $problemRepository;
        $this->logoRepository = $logoRepository;
        $this->problemTestAssociationRepository = $problemTestAssociationRepository;
        $this->testStatisticsFormFactory = $testStatisticsFormFactory;
        $this->fileService = $fileService;
        $this->validator = $validator;
        $this->testGeneratorService = $testGeneratorService;
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function actionRegenerate(int $id): void
    {
        bdump('ACTION REGENERATE');
        $this->actionUpdate($id);
        bdump($this['entityForm']->getEntity());
    }

    /**
     * @param array $filters
     * @throws \Exception
     */
    public function handleFilterChange(array $filters): void
    {
        $this['entityForm']->handleFilterChange($filters);
    }

    /**
     * @param $name
     * @return DataGrid
     */
    public function createComponentEntityGrid($name): DataGrid
    {
        $grid = $this->gridFactory->create($this, $name);
        $grid->addAction('close', '', 'close!')
            ->setTemplate(__DIR__ . '/templates/Test/dataGridActions/closeAction.latte');
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
        $grid->addAction('update', '', 'update!')
            ->setIcon('edit')
            ->setClass('btn btn-primary btn-sm')
            ->setTitle('Editovat');
        return $grid;
    }

    /**
     * @param int $id
     */
    public function handleClose(int $id): void
    {
        try{
            $test = $this->functionality->close($id);
            $template = $this->getTemplate();
            $template->setFile(TEMPLATES_DIR . '/pdf/testPdf/default.latte');
            $this->testGeneratorService->createTestData($test, $template);
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('close', true,'error', $e, true));
            return;
        }
        // Set template back to test/default
        $template->setFile(__DIR__ . '/templates/Test/default.latte');
        $this->informUser(new UserInformArgs('close', true, 'success', null, true));
        $this['entityGrid']->reload();
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handlePdfOverleaf(int $id): void
    {
        $this->fileService->moveTestDirToPublic($id);
        $this->redirectUrl('https://www.overleaf.com/docs?snip_uri=http://wiedzmin.4fan.cz/data_public/tests/test_' . $id . '.zip');
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleDownloadSource(int $id): void
    {
        $this->sendResponse(new CallbackResponse(static function (IRequest $request, IResponse $response) use ($id) {
            $response = new Response();
            $response->setHeader('Content-type', 'application/zip');
            $response->setHeader(
                'Content-Disposition',
                'attachment; filename=test_' . $id . '.zip'
            );
            $response->setHeader(
                'Content-length',
                filesize(DATA_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . 'test_' . $id . '.zip')
            );
            $response->setHeader('Pragma', 'no-cache');
            $response->setHeader('Expires', '0');

            readfile(DATA_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . 'test_' . $id . '.zip');

        }));
    }

    /**
     * @return EntityFormControl
     */
    public function createComponentEntityForm(): EntityFormControl
    {
        $control = $this->formFactory->create();
        $control->onSuccess[] = function () use ($control) {
            bdump('SUCCESS');
            bdump($this->getTemplate());
            $this->informUser(new UserInformArgs($this->getAction(), true, 'success', null, false, 'entityForm'));
            $this->reloadEntity();
            if($control->isUpdate()){
                $control->redrawControl();
            }
        };
        $control->onError[] = function ($e) {
            bdump($e);
            $this->informUser(new UserInformArgs($this->getAction(), true, 'error', $e, false, 'entityForm'));
        };
        return $control;
    }

    public function renderCreate(): void
    {
        if($this->getParameter('filters') === null){
            $this->getEntityForm()->fillComponents();
        }
    }

    public function renderRegenerate(): void
    {
        $this->getEntityForm()->fillComponents();
    }
}
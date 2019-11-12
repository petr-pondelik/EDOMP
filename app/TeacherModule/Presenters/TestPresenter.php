<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 2.3.19
 * Time: 14:48
 */

namespace App\TeacherModule\Presenters;

use App\CoreModule\Arguments\UserInformArgs;
use App\TeacherModule\Components\DataGrids\TestGridFactory;
use App\CoreModule\Components\Forms\EntityFormControl;
use App\TeacherModule\Components\Forms\TestForm\ITestFormFactory;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Model\Persistent\Functionality\TestFunctionality;
use App\CoreModule\Model\Persistent\Repository\LogoRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\ProblemTemplateRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemFinalTestVariantAssociationRepository;
use App\CoreModule\Model\Persistent\Repository\TestRepository;
use App\CoreModule\Services\Authorizator;
use App\CoreModule\Services\FileService;
use App\TeacherModule\Services\FilterSession;
use App\CoreModule\Services\Validator;
use App\TeacherModule\Services\NewtonApiClient;
use App\TeacherModule\Services\TestGenerator;
use Nette\Application\Responses\CallbackResponse;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Http\Response;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class TestPresenter
 * @package App\TeacherModule\Presenters
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
     * @var FileService
     */
    protected $fileService;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var TestGenerator
     */
    protected $testGenerator;

    /**
     * @var FilterSession
     */
    protected $filterSession;

    /**
     * TestPresenter constructor.
     * @param Authorizator $authorizator
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param TestRepository $testRepository
     * @param TestFunctionality $testFunctionality
     * @param ProblemTemplateRepository $problemTemplateRepository
     * @param ProblemRepository $problemRepository
     * @param LogoRepository $logoRepository
     * @param ProblemFinalTestVariantAssociationRepository $problemTestAssociationRepository
     * @param ITestFormFactory $testCreateFormFactory
     * @param TestGridFactory $testGridFactory
     * @param FileService $fileService
     * @param TestGenerator $testGenerator
     * @param IHelpModalFactory $sectionHelpModalFactory
     * @param FilterSession $filterSession
     */
    public function __construct
    (
        Authorizator $authorizator, Validator $validator, NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        TestRepository $testRepository, TestFunctionality $testFunctionality,
        ProblemTemplateRepository $problemTemplateRepository, ProblemRepository $problemRepository, LogoRepository $logoRepository,
        ProblemFinalTestVariantAssociationRepository $problemTestAssociationRepository,
        ITestFormFactory $testCreateFormFactory, TestGridFactory $testGridFactory,
        FileService $fileService,
        TestGenerator $testGenerator,
        IHelpModalFactory $sectionHelpModalFactory,
        FilterSession $filterSession
    )
    {
        parent::__construct
        (
            $authorizator, $validator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory,
            $testRepository, $testFunctionality, $testGridFactory, $testCreateFormFactory
        );
        $this->problemTemplateRepository = $problemTemplateRepository;
        $this->problemRepository = $problemRepository;
        $this->logoRepository = $logoRepository;
        $this->problemTestAssociationRepository = $problemTestAssociationRepository;
        $this->fileService = $fileService;
        $this->validator = $validator;
        $this->testGenerator = $testGenerator;
        $this->filterSession = $filterSession;
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function actionRegenerate(int $id): void
    {
        bdump('ACTION REGENERATE');

        if (!$entity = $this->safeFind($id)) {
            $this->redirect('default');
        }

        if (!$entity->isClosed()) {
            $this->flashMessage('Pro přegenerování je nutné test nejprve uzavřít.', 'danger');
            $this->redirect('default');
        }

        if (!$this->isEntityAllowed($entity)) {
            $this->flashMessage('Nedostatečná přístupová práva.', 'danger');
            $this->redirect('default');
        }

        $formControl = $this['entityForm'];
        $formControl->setEntity($entity);
        $this->getEntityForm()->initComponents();
        $this->template->entity = $entity;

        if (!$formControl->isSubmitted()) {
            $formControl->setDefaults();
        }
    }

    /**
     * @param int $key
     * @param array $filters
     */
    public function handleFilterChange(int $key, array $filters): void
    {
        bdump($this->getParameters());
        $this['entityForm']->handleFilterChange($key, $filters);
    }

    /**
     * @param array $filters
     */
    public function handleSetFilters(array $filters): void
    {
        bdump('HANDLE SET FILTERS');
        $this->filterSession->setFilters($filters);
        bdump($this->filterSession->getFilters());
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
            ->setClass('btn btn-danger btn-sm ajax')
            ->setTitle('Odstranit test')
            ->addAttributes([
                'data-toggle' => 'tooltip'
            ]);

        $grid->addAction('downloadSource', '', 'downloadSource!')
            ->setTemplate(__DIR__ . '/templates/Test/dataGridActions/downloadSource.latte');

        $grid->addAction('pdf', '', 'pdfOverleaf!')
            ->setTemplate(__DIR__ . '/templates/Test/dataGridActions/pdfOverleaf.latte');

        $grid->addAction('regenerate', '', 'regenerate!')
            ->setTemplate(__DIR__ . '/templates/Test/dataGridActions/regenerateAction.latte');

        $grid->addAction('update', '', 'update!')
            ->setIcon('edit')
            ->setClass('btn btn-primary btn-sm')
            ->setTitle('Upravit test');

        return $grid;
    }

    /**
     * @param int $id
     */
    public function handleClose(int $id): void
    {
        try {
            $test = $this->functionality->close($id);
            $template = $this->getTemplate();
            $template->setFile(TEACHER_MODULE_TEMPLATES_DIR . '/pdf/testPdf/active.latte');
            $this->testGenerator->createTestData($test, $template);
        } catch (\Exception $e) {
            $this->informUser(new UserInformArgs('close', true, 'error', $e, true));
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
        bdump('HANDLE PDF OVERLEAF');
        bdump($id);

        if (!$entity = $this->safeFind($id)) {
            bdump('FAIL');
            $this->redirect('default');
        }

        if (!$entity->isClosed()) {
            $this->flashMessage('Pro kompilaci je potřeba test nejprve uzavřít.', 'danger');
            return;
        }

        $this->fileService->moveTestDirToPublic($id);
        $this->redirectUrl('https://www.overleaf.com/docs?snip_uri=http://wiedzmin.4fan.cz/data_public/tests/test_' . $id . '.zip');
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleRegenerate(int $id): void
    {
        $this->redirect('regenerate', $id);
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleDownloadSource(int $id): void
    {
        bdump('HANDLE DOWNLOAD SOURCE');
        bdump($id);

        if (!$entity = $this->safeFind($id)) {
            return;
        }

        if(!$entity->isClosed()){
            $this->flashMessage('Pro stažení archivu je potřeba test nejprve uzavřít.', 'danger');
            $this->redrawControl('mainFlashesSnippet');
            return;
        }

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
            if ($control->isUpdate()) {
                $this->informUser(new UserInformArgs($this->getAction(), true, 'success', null, false, 'entityForm'));
                $this->reloadEntity();
                $control->redrawControl();
            } else {
                $this->informUser(new UserInformArgs($this->getAction(), false, 'success', null, false));
                $this->redirect('default');
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
        bdump('RENDER CREATE');
        bdump($this->filterSession->getFilters());
        if(!$this->isAjax()){
            $this->getEntityForm()->fillComponents($this->filterSession->getFilters());
        }
    }

    public function renderRegenerate(): void
    {
        $this->getEntityForm()->fillComponents();
    }
}
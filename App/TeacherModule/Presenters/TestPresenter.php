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
use App\TeacherModule\Services\TestDownloader;
use App\TeacherModule\Services\TestGenerator;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class TestPresenter
 * @package App\TeacherModule\Presenters
 */
final class TestPresenter extends EntityPresenter
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
     * @var TestDownloader
     */
    protected $testDownloader;

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
     * @param TestDownloader $testDownloader
     */
    public function __construct
    (
        Authorizator $authorizator,
        Validator $validator,
        NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory,
        ISideBarFactory $sideBarFactory,
        FlashesTranslator $flashesTranslator,
        TestRepository $testRepository,
        TestFunctionality $testFunctionality,
        ProblemTemplateRepository $problemTemplateRepository,
        ProblemRepository $problemRepository,
        LogoRepository $logoRepository,
        ProblemFinalTestVariantAssociationRepository $problemTestAssociationRepository,
        ITestFormFactory $testCreateFormFactory,
        TestGridFactory $testGridFactory,
        FileService $fileService,
        TestGenerator $testGenerator,
        IHelpModalFactory $sectionHelpModalFactory,
        FilterSession $filterSession,
        TestDownloader $testDownloader
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
        $this->testDownloader = $testDownloader;
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
        $this['entityForm']->handleFilterChange($key, $filters);
    }

    /**
     * @param array $filters
     */
    public function handleSetFilters(array $filters): void
    {
        bdump('HANDLE SET FILTERS');
        $this->filterSession->setFilters($filters);
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
            ->setTitle('Odstranit test');

        $grid->addAction('downloadSource', '', 'downloadSource!')
            ->setTemplate(__DIR__ . '/templates/Test/dataGridActions/downloadSource.latte');

        $grid->addAction('pdf', '', 'pdfOverleaf!')
            ->setTemplate(__DIR__ . '/templates/Test/dataGridActions/pdfOverleaf.latte');

        $grid->addAction('regenerate', '', 'regenerate!')
            ->setTemplate(__DIR__ . '/templates/Test/dataGridActions/regenerateAction.latte');

        $grid->addAction('update', '', 'update!')
            ->setIcon('edit')
            ->setClass('btn btn-primary btn-sm')
            ->setTitle('Editovat test');

        return $grid;
    }

    /**
     * @param int $id
     * @throws \App\CoreModule\Exceptions\FlashesTranslatorException
     */
    public function handleClose(int $id): void
    {
        try {
            $test = $this->functionality->close($id);
            $this->testGenerator->createTestData($test);
        } catch (\Exception $e) {
            $this->informUser(new UserInformArgs('close', true, 'error', $e, 'flashesModal'));
            return;
        }
        $this->informUser(new UserInformArgs('close', true, 'success', null, 'flashesModal'));
        $this['entityGrid']->reload();
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handlePdfOverleaf(int $id): void
    {
        bdump('HANDLE PDF OVERLEAF');

        if (!$entity = $this->safeFind($id)) {
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
     * @throws \Nette\Application\BadRequestException
     */
    public function handleDownloadSource(int $id): void
    {
        bdump('HANDLE DOWNLOAD SOURCE');

        if (!$entity = $this->safeFind($id)) {
            return;
        }

        if (!$entity->isClosed()) {
            $this->flashMessage('Pro stažení archivu je potřeba test nejprve uzavřít.', 'danger');
            $this->redrawControl('mainFlashesSnippet');
            return;
        }

        $this->sendResponse($this->testDownloader->download($id));
    }

    /**
     * @return EntityFormControl
     */
    public function createComponentEntityForm(): EntityFormControl
    {
        $control = $this->formFactory->create();
        $control->onSuccess[] = function () use ($control) {
            if ($control->isUpdate()) {
                $this->informUser(new UserInformArgs($this->getAction(), true, 'success', null,'flashesModal'));
                $this->reloadEntity();
                $control->redrawControl();
            } else {
                $this->informUser(new UserInformArgs($this->getAction(), false, 'success', null, 'flashesModal'));
                $this->redirect('default');
            }
        };
        $control->onError[] = function ($e) {
            bdump($e);
            $this->informUser(new UserInformArgs($this->getAction(), true, 'error', $e, 'entityForm'));
        };
        return $control;
    }

    public function renderCreate(): void
    {
        bdump('RENDER CREATE');
        if (!$this->isAjax()) {
            $this->getEntityForm()->fillComponents($this->filterSession->getFilters());
        }
    }

    public function renderRegenerate(): void
    {
        $this->getEntityForm()->fillComponents();
    }
}
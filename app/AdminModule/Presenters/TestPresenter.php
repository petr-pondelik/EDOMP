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
use App\Components\Forms\TestForm\ITestFormFactory;
use App\Components\Forms\TestForm\TestFormControl;
use App\Components\Forms\TestStatisticsForm\TestStatisticsFormControl;
use App\Components\Forms\TestStatisticsForm\TestStatisticsFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\FlashesTranslator;
use App\Model\Entity\Test;
use App\Model\Functionality\TestFunctionality;
use App\Model\Repository\LogoRepository;
use App\Model\Repository\ProblemRepository;
use App\Model\Repository\ProblemTemplateRepository;
use App\Model\Repository\ProblemFinalTestVariantAssociationRepository;
use App\Model\Repository\TestRepository;
use App\Services\Authorizator;
use App\Services\FileService;
use App\Services\NewtonApiClient;
use App\Services\Validator;
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
     * @var ProblemFinalTestVariantAssociationRepository
     */
    protected $problemTestAssociationRepository;

    /**
     * @var ITestFormFactory
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
     * @var Validator
     */
    protected $validator;

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
     * @param ProblemFinalTestVariantAssociationRepository $problemTestAssociationRepository
     * @param ITestFormFactory $testFormFactory
     * @param TestStatisticsFormFactory $testStatisticsFormFactory
     * @param TestGridFactory $testGridFactory
     * @param FileService $fileService
     * @param Validator $validator
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        TestRepository $testRepository, TestFunctionality $testFunctionality,
        ProblemTemplateRepository $problemTemplateRepository, ProblemRepository $problemRepository, LogoRepository $logoRepository,
        ProblemFinalTestVariantAssociationRepository $problemTestAssociationRepository,
        ITestFormFactory $testFormFactory, TestStatisticsFormFactory $testStatisticsFormFactory, TestGridFactory $testGridFactory,
        FileService $fileService, Validator $validator,
        ISectionHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct($authorizator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory);
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
        $this->validator = $validator;
    }

    /**
     * @param int $id
     */
    public function actionStatistics(int $id): void
    {
        $form = $this['testStatisticsForm']['form'];
        if(!$form->isSubmitted()){
            $this->template->id = $id;
            $test = $this->testRepository->find($id);
            $this->setDefaults($form, $test);
        }
    }

    /**
     * @param IComponent $form
     * @param Test $test
     */
    public function setDefaults(IComponent $form, Test $test): void
    {
        $testVariants = $test->getTestVariants()->getValues();

        $form->setDefaults([
            'test_id' => $test->getId(),
            'variants_cnt' => count($testVariants),
            'problems_per_variant' => count($testVariants[0]->getProblemFinalAssociations()->getValues())
        ]);

        $i = 0;
        foreach($testVariants as $testVariant){
            $j = 0;
            foreach ($testVariant->getProblemFinalAssociations()->getValues() as $problemFinalAssociation) {
                $problemFinal = $problemFinalAssociation->getProblemFinal();
                $form->setDefaults([
                    'problem_final_id_disabled_' . $i . '_' . $j => $problemFinal->getId(),
                    'problem_final_id_' . $i . '_' . $j => $problemFinal->getId(),
                    'success_rate_' . $i . '_' . $j => $problemFinalAssociation->getSuccessRate()
                ]);
                if($problemTemplate = $problemFinalAssociation->getProblemTemplate()){
                    bdump($problemTemplate->getId());
                    $form->setDefaults([
                        'problem_template_id_disabled_' . $i . '_' . $j => $problemTemplate->getId(),
                        'problem_template_id_' . $i . '_' . $j => $problemTemplate->getId()
                    ]);
                }
                $j++;
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
    public function createComponentTestGrid($name): void
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
        $grid->addAction('statistics', '', 'statistics!')
            ->setIcon('percent')
            ->setClass('btn btn-primary btn-sm')
            ->setTitle('Statistika úspěšnosti');
    }

    /**
     * @param int $id
     */
    public function handleDelete(int $id): void
    {
        try{
            $this->testFunctionality->delete($id);
        } catch (\Exception $e){
            $this->informUser(new UserInformArgs('delete', true, 'error', $e));
            return;
        }
        $this['testGrid']->reload();
        $this->informUser(new UserInformArgs('delete', true));
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
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleStatistics(int $id): void
    {
        $this->redirect('statistics', $id);
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
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
use App\Components\Forms\TestStatisticsFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\ConstHelper;
use App\Helpers\FlashesTranslator;
use App\Helpers\LatexHelper;
use App\Helpers\StringsHelper;
use App\Model\Functionality\ProblemFunctionality;
use App\Model\Functionality\ProblemTestAssociationFunctionality;
use App\Model\Functionality\TestFunctionality;
use App\Model\Repository\LogoRepository;
use App\Model\Repository\ProblemRepository;
use App\Model\Repository\ProblemTemplateRepository;
use App\Model\Repository\ProblemTestAssociationRepository;
use App\Model\Repository\TestRepository;
use App\Service\Authorizator;
use App\Service\FileService;
use App\Service\GeneratorService;
use App\Service\MathService;
use App\Service\TestBuilderService;
use App\Service\ValidationService;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\Responses\CallbackResponse;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Http\Response;
use Nette\NotSupportedException;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;
use Nette\Utils\FileSystem;

/**
 * Class TestPresenter
 * @package app\presenters
 */
class TestPresenter extends AdminPresenter
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

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
     * @var ProblemFunctionality
     */
    protected $problemFunctionality;

    /**
     * @var LogoRepository
     */
    protected $logoRepository;

    /**
     * @var ProblemTestAssociationRepository
     */
    protected $problemTestAssociationRepository;

    /**
     * @var ProblemTestAssociationFunctionality
     */
    protected $problemTestAssociationFunctionality;

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
     * @var TestBuilderService
     */
    protected $testBuilderService;

    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * @var ValidationService
     */
    protected $validationService;

    /**
     * @var GeneratorService
     */
    protected $generatorService;

    /**
     * @var MathService
     */
    protected $mathService;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * @var LatexHelper
     */
    protected $latexHelper;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * TestPresenter constructor.
     * @param Authorizator $authorizator
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param EntityManager $entityManager
     * @param TestRepository $testRepository
     * @param TestFunctionality $testFunctionality
     * @param ProblemTemplateRepository $problemTemplateRepository
     * @param ProblemRepository $problemRepository
     * @param ProblemFunctionality $problemFunctionality
     * @param LogoRepository $logoRepository
     * @param ProblemTestAssociationRepository $problemTestAssociationRepository
     * @param ProblemTestAssociationFunctionality $problemTestAssociationFunctionality
     * @param TestFormFactory $testFormFactory
     * @param TestStatisticsFormFactory $testStatisticsFormFactory
     * @param TestGridFactory $testGridFactory
     * @param TestBuilderService $testBuilderService
     * @param FileService $fileService
     * @param ValidationService $validationService
     * @param GeneratorService $generatorService
     * @param MathService $mathService
     * @param StringsHelper $stringsHelper
     * @param LatexHelper $latexHelper
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        Authorizator $authorizator,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        EntityManager $entityManager,
        TestRepository $testRepository, TestFunctionality $testFunctionality,
        ProblemTemplateRepository $problemTemplateRepository,
        ProblemRepository $problemRepository, ProblemFunctionality $problemFunctionality,
        LogoRepository $logoRepository,
        ProblemTestAssociationRepository $problemTestAssociationRepository, ProblemTestAssociationFunctionality $problemTestAssociationFunctionality,
        TestFormFactory $testFormFactory, TestStatisticsFormFactory $testStatisticsFormFactory, TestGridFactory $testGridFactory,
        TestBuilderService $testBuilderService, FileService $fileService, ValidationService $validationService,
        GeneratorService $generatorService, MathService $mathService,
        StringsHelper $stringsHelper, LatexHelper $latexHelper, ConstHelper $constHelper
    )
    {
        parent::__construct($authorizator, $headerBarFactory, $sideBarFactory, $flashesTranslator);
        $this->entityManager = $entityManager;
        $this->testRepository = $testRepository;
        $this->testFunctionality = $testFunctionality;
        $this->problemTemplateRepository = $problemTemplateRepository;
        $this->problemRepository = $problemRepository;
        $this->problemFunctionality = $problemFunctionality;
        $this->logoRepository = $logoRepository;
        $this->problemTestAssociationRepository = $problemTestAssociationRepository;
        $this->problemTestAssociationFunctionality = $problemTestAssociationFunctionality;
        $this->testFormFactory = $testFormFactory;
        $this->testStatisticsFormFactory = $testStatisticsFormFactory;
        $this->testGridFactory = $testGridFactory;
        $this->testBuilderService = $testBuilderService;
        $this->fileService = $fileService;
        $this->validationService = $validationService;
        $this->generatorService = $generatorService;
        $this->mathService = $mathService;
        $this->stringsHelper = $stringsHelper;
        $this->latexHelper = $latexHelper;
        $this->constHelper = $constHelper;
    }

    /**
     * @param int $id
     */
    public function actionStatistics(int $id)
    {
        $form = $this["statisticsForm"];
        if(!$form->isSubmitted()){
            $this->template->id = $id;
            $problemAssociations = $this->problemTestAssociationRepository->findBy(["test" => $id]);
            $this->template->problemsCnt = count($problemAssociations);
            $this->template->problemAssociations = $problemAssociations;
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
        $form["problems_cnt"]->setDefaultValue(count($problemAssociations));
        $i = 0;
        $form["test_id"]->setDefaultValue($testId);
        foreach($problemAssociations as $association){
            $form["problem_final_id_disabled_" . $i]->setDefaultValue($association->getProblem()->getId());
            $form["problem_final_id_" . $i]->setDefaultValue($association->getProblem()->getId());
            if($association->getProblemTemplate()){
                $form["problem_prototype_id_disabled_" . $i]->setDefaultValue($association->getProblemTemplate()->getId());
                $form["problem_prototype_id_" . $i]->setDefaultValue($association->getProblemTemplate()->getId());
            }
            $form["success_rate_" . $i]->setDefaultValue($association->getSuccessRate());
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
            $this->redirect('default');
        };
        $control->onError[] = function ($e){
            $this->informUser(new UserInformArgs('create', false, 'error', $e));
            $this->redirect('default');
        };
        return $control;
    }

    /**
     * @return Form
     */
    public function createComponentStatisticsForm()
    {
        $form = $this->testStatisticsFormFactory->create();
        $form->onSuccess[] = [$this, "handleStatisticsFormSuccess"];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Exception
     */
    public function handleStatisticsFormSuccess(Form $form, ArrayHash $values)
    {
        bdump($values);
        for($i = 0; $i < $values->problems_cnt; $i++) {
            if(!empty($values->{"success_rate_" . $i})){
                $this->problemTestAssociationFunctionality->update(
                    $values->{"problem_final_id_" . $i},
                    ArrayHash::from([
                        "test_id" => $values->test_id,
                        "success_rate" => $values->{"success_rate_" . $i}
                        ])
                );
            }
        }

        for($i = 0; $i < $values->problems_cnt; $i++) {
            $this->problemFunctionality->calculateSuccessRate($values->{"problem_final_id_" . $i});
            if(!empty($values->{"problem_prototype_id_" . $i}))
                $this->problemFunctionality->calculateSuccessRate($values->{"problem_prototype_id_" . $i}, true);
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 2.3.19
 * Time: 14:48
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\TestGridFactory;
use App\Components\Forms\TestFormFactory;
use App\Components\Forms\TestStatisticsFormFactory;
use App\Helpers\ConstHelper;
use App\Helpers\LatexHelper;
use App\Helpers\StringsHelper;
use App\Model\Functionality\ProblemFunctionality;
use App\Model\Functionality\ProblemTestAssociationFunctionality;
use App\Model\Functionality\TestFunctionality;
use App\Model\Repository\LogoRepository;
use App\Model\Repository\ProblemFinalRepository;
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
use Nette\Application\Responses\TextResponse;
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
        parent::__construct($authorizator);
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

    public function renderCreate()
    {
        $this->template->logos = $this->logoRepository->findBy([], ["id" => "DESC"]);
        $this->testRepository->findVariants(22);
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
        bdump($filters);

        foreach($filters as $problemKey => $problemFilters){

            bdump($problemFilters);

            if(!isset($problemFilters["filters"]["is_template"]) || $problemFilters["filters"]["is_template"])
                $filterRes = $this->problemTemplateRepository->findFiltered($problemFilters["filters"]);
            else
                $filterRes = $this->problemRepository->findFiltered($problemFilters["filters"]);

            bdump($filterRes);

            if(isset($problemFilters['filters'])){
                foreach ($problemFilters['filters'] as $filterType => $filterVal) {
                    $this['createForm'][$filterType . '_' . $problemKey]->setValue($filterVal);
                }
            }

            $this['createForm']['problem_' . $problemKey]->setItems($filterRes);

            if(array_key_exists($problemFilters['selected'], $filterRes))
                $this['createForm']['problem_' . $problemKey]->setValue($problemFilters['selected']);

        }

        $this->redrawControl('testCreateFormSnippet');
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
        //TODO: Create response decorator as Class

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
     * @return \Nette\Application\UI\Form
     * @throws \Exception
     */
    public function createComponentCreateForm()
    {
        $form =  $this->testFormFactory->create();
        $form->onValidate[] = [$this, 'handleFormValidate'];
        $form->onSuccess[] = [$this, 'handleCreateFormSuccess'];
        return $form;
    }

    /**
     * @param Form $form
     * @param $values
     * @throws \Nette\Application\AbortException
     * @throws \Nette\Utils\JsonException
     */
    public function handleCreateFormSuccess(Form $form, $values)
    {
        try{
            $testData = $this->testBuilderService->buildTest($values);
        }
        catch(NotSupportedException $e){
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redrawControl('flashesSnippet');
            return;
        }

        $template = $this->getTemplate();

        $template->setFile(__DIR__.'/templates/Test/export.latte');

        $this->entityManager->flush();

        $test = $this->testRepository->find($testData->testId);

        bdump($this->testRepository->find($testData->testId));

        foreach($testData->variants as $variant){
            $template->variant = $variant;
            $template->test = $test;
            FileSystem::createDir( DATA_DIR.'/tests/'.$testData->testId);
            file_put_contents( DATA_DIR.'/tests/'.$testData->testId.'/variant_'.Strings::lower($variant).'.tex', (string) $template);
        }

        $this->fileService->createTestZip($testData->testId);

        $this->redirect('default');
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form)
    {
        $values = $form->getValues();

        bdump($values);

        $validateFields["logo_file"] = $values->logo_file_hidden;
        $validateFields["school_year"] = $values->school_year;
        $validateFields["test_number"] = $values->test_number;

        $validationErrors = $this->validationService->validate($validateFields);

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $form[$veKey]->addError($error);
            }
        }

        $this->redrawControl("logoFileErrorSnippet");
        $this->redrawControl("schoolYearErrorSnippet");
        $this->redrawControl("testNumberErrorSnippet");
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

    public function handleGetRes()
    {
        //$problem = $this->problemManager->getById(584);
        //bdump($problem);

        //bdump($this->stringsHelper::splitByParameters($problem->structure));
        //bdump($this->stringsHelper::getParametrized($problem->structure));

        //$this->latexHelper::parseLatex("\( \bigg(x+1\bigg)^2 + x + 3 = -3 x^2 \)");

        //bdump($this->latexHelper::parseExponent("3^{n+1}"));
        //bdump($this->problemManager->getByTestId(19));
        //bdump($this->problemManager->isInUsage(630));
        //bdump(Passwords::hash("6ysaz7dt"));

        /*bdump($this->validationService->validateQuadraticEquation("1/4 y + p0 + p1^2", "y"));

        bdump($this->stringsHelper::isEquation("15 x + 20 + p0 + 10 = 5"));*/

        bdump(Strings::match("2 x + 15 x + 20 = 5", "~^\w_\w~"));
    }

}
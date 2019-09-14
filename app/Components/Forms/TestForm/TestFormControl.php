<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.5.19
 * Time: 17:39
 */

namespace App\Components\Forms\TestForm;


use App\Arguments\ValidatorArgument;
use App\Components\Forms\FormControl;
use App\Components\LogoDragAndDrop\ILogoDragAndDropFactory;
use App\Components\LogoDragAndDrop\LogoDragAndDropControl;
use App\Components\ProblemStack\IProblemStackFactory;
use App\Model\Persistent\Entity\ProblemConditionType;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\GroupRepository;
use App\Model\Persistent\Repository\LogoRepository;
use App\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\Model\Persistent\Repository\ProblemFinal\ProblemFinalRepository;
use App\Model\Persistent\Repository\ProblemRepository;
use App\Model\Persistent\Repository\ProblemTemplate\ProblemTemplateRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Model\Persistent\Repository\TestRepository;
use App\Services\FileService;
use App\Services\TestGeneratorService;
use App\Services\Validator;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Nette\Utils\Strings;

/**
 * Class TestFormControl
 * @package App\Components\Forms\TestForm
 */
class TestFormControl extends FormControl
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
     * @var ProblemRepository
     */
    protected $problemRepository;

    /**
     * @var ProblemTemplateRepository
     */
    protected $problemTemplateRepository;

    /**
     * @var ProblemFinalRepository
     */
    protected $problemFinalRepository;

    /**
     * @var ProblemTypeRepository
     */
    protected $problemTypeRepository;

    /**
     * @var DifficultyRepository
     */
    protected $difficultyRepository;

    /**
     * @var LogoRepository
     */
    protected $logoRepository;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @var SubCategoryRepository
     */
    protected $subCategoryRepository;

    /**
     * @var ProblemConditionTypeRepository
     */
    protected $problemConditionTypeRepository;

    /**
     * @var TestGeneratorService
     */
    protected $testGeneratorService;

    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * @var ILogoDragAndDropFactory
     */
    protected $logoDragAndDropFactory;

    /**
     * @var IProblemStackFactory
     */
    protected $problemStackFactory;

    /**
     * @var ProblemConditionType[]
     */
    protected $problemConditionTypes;

    /**
     * @var int
     */
    protected $maxProblems;

    /**
     * TestFormControl constructor.
     * @param Validator $validator
     * @param EntityManager $entityManager
     * @param TestRepository $testRepository
     * @param ProblemRepository $problemRepository
     * @param ProblemTemplateRepository $problemTemplateRepository
     * @param ProblemFinalRepository $problemFinalRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param DifficultyRepository $difficultyRepository
     * @param LogoRepository $logoRepository
     * @param GroupRepository $groupRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param TestGeneratorService $testGeneratorService
     * @param FileService $fileService
     * @param ILogoDragAndDropFactory $logoDragAndDropFactory
     * @param IProblemStackFactory $problemStackFactory
     * @throws \Exception
     */
    public function __construct
    (
        Validator $validator, EntityManager $entityManager,
        TestRepository $testRepository,
        ProblemRepository $problemRepository, ProblemTemplateRepository $problemTemplateRepository, ProblemFinalRepository $problemFinalRepository,
        ProblemTypeRepository $problemTypeRepository,
        DifficultyRepository $difficultyRepository, LogoRepository $logoRepository, GroupRepository $groupRepository,
        SubCategoryRepository $subCategoryRepository,
        ProblemConditionTypeRepository $problemConditionTypeRepository,
        TestGeneratorService $testGeneratorService, FileService $fileService,
        ILogoDragAndDropFactory $logoDragAndDropFactory, IProblemStackFactory $problemStackFactory
    )
    {
        parent::__construct($validator);

        $this->entityManager = $entityManager;
        $this->testRepository = $testRepository;
        $this->problemRepository =$problemRepository;
        $this->problemTemplateRepository = $problemTemplateRepository;
        $this->problemFinalRepository = $problemFinalRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->difficultyRepository = $difficultyRepository;
        $this->logoRepository = $logoRepository;
        $this->groupRepository = $groupRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->problemConditionTypeRepository = $problemConditionTypeRepository;
        $this->testGeneratorService = $testGeneratorService;
        $this->fileService = $fileService;
        $this->logoDragAndDropFactory = $logoDragAndDropFactory;
        $this->problemStackFactory = $problemStackFactory;
        $this->problemConditionTypes = $this->problemConditionTypeRepository->findAssoc([], 'id');
    }

    /**
     * @param array $params
     * @throws \Nette\Application\BadRequestException
     */
    public function loadState(array $params): void
    {
        parent::loadState($params);

        $problems = $this->problemRepository->findAssoc([], 'id');

        $this->maxProblems = $this->presenter->context->parameters['testMaxProblems'];

        for ($i = 0; $i < $this->maxProblems; $i++){
            $this->addComponent($this->problemStackFactory->create(), 'problemStack' . $i);
        }

        for ($i = 0; $i < $this->maxProblems; $i++){
            $this['problemStack' . $i]->setProblems($problems);
        }
    }

    /**
     * @return LogoDragAndDropControl
     */
    public function createComponentLogoDragAndDrop(): LogoDragAndDropControl
    {
        return $this->logoDragAndDropFactory->create();
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $problemTypes = $this->problemTypeRepository->findAssoc([], 'id');
        $difficulties = $this->difficultyRepository->findAssoc([], 'id');
        $groups = $this->groupRepository->findAllowed($this->presenter->user);
        $subCategories = $this->subCategoryRepository->findAssoc([], 'id');
        $logos = $this->logoRepository->findAssoc([],'id');
        $problems = $this->problemRepository->findAssoc([], 'id');

        $conditionTypesByProblemTypes = [];
        foreach ($problemTypes as $id => $problemType){
            foreach ($problemType->getConditionTypes()->getValues() as $conditionType){
                $conditionTypesByProblemTypes[$id] = $conditionType->getId();
            }
        }

        $form->addSelect('variants', 'Počet variant *', [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
            6 => 6,
            7 => 7,
            8 => 8
        ])
            ->setHtmlAttribute('class', 'form-control col-12 selectpicker')
            ->setDefaultValue(true);

        $form->addHidden('problems_cnt')->setDefaultValue(1)
            ->setHtmlId('problemsCnt');

        $form->addSelect('logo', 'Logo *', $logos)
            ->setPrompt('Zvolte logo')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('test-logo');

        $form->addMultiSelect('groups', 'Skupiny *', $groups)
            ->setHtmlAttribute('class', 'form-control selectpicker')
            ->setHtmlAttribute('title', 'Zvolte skupiny');

        $form->addText('testTerm', 'Období *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte období ve školním roce.');

        $form->addText('schoolYear', 'Školní rok *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'rrrr/rr(rr) nebo rrrr-rr(rr)');

        $form->addInteger('testNumber', 'Číslo testu *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte číslo testu.');

        // Úvodní text se zobrazí pod hlavičkou testu
        $form->addTextArea('introductionText', 'Úvodní text')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte úvodní text testu.');

        for($i = 0; $i < $this->maxProblems; $i++) {

            $form->addSelect('is_template_' . $i, 'Šablona', [
                1 => 'Ano',
                0 => 'Ne'
            ])
                ->setPrompt('Zvolte')
                ->setHtmlAttribute('class', 'form-control filter selectpicker')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'is_template')
                ->setHtmlId('is_template_'.$i);

            $form->addMultiSelect('sub_category_id_' . $i, 'Téma', $subCategories)
                ->setHtmlAttribute('class', 'form-control filter selectpicker')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'sub_category_id')
                ->setHtmlAttribute('title', 'Zvolte témata')
                ->setHtmlId('sub_category_id_' . $i);

            $form->addMultiSelect('problem_type_id_' . $i, 'Typ', $problemTypes)
                ->setHtmlAttribute('class', 'form-control filter problem-type-filter selectpicker')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'problem_type_id')
                ->setHtmlAttribute('data-condition-types', Json::encode($conditionTypesByProblemTypes) )
                ->setHtmlAttribute('title', 'Zvolte typy')
                ->setHtmlId('problem_type_id_' . $i);

            $form->addMultiSelect('difficulty_id_' . $i, 'Obtížnost', $difficulties)
                ->setHtmlAttribute('class', 'form-control filter selectpicker')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'difficulty_id')
                ->setHtmlAttribute('title', 'Zvolte obtížnosti')
                ->setHtmlId('difficulty_id_' . $i);

            foreach ($this->problemConditionTypes as $conditionType){

                $form->addMultiSelect('condition_type_id_' . $conditionType->getId() . '_' . $i, $conditionType->getLabel(),
                    $conditionType->getProblemConditions()->getValues()
                )
                    ->setHtmlAttribute('class', 'form-control filter selectpicker')
                    ->setHtmlAttribute('data-problem-id', $i)
                    ->setHtmlAttribute('data-filter-type', 'condition_type_id_' . $conditionType->getId())
                    ->setHtmlAttribute('title', $conditionType->getPrompt());

            }

            $form->addMultiSelect('problem_'.$i, 'Zvolené úlohy', $problems)
                ->setHtmlAttribute('class', 'form-control filter problem-select')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlId('problem_'.$i);

            $form->addCheckbox('newpage_' . $i, 'Nová stránka');

        }

        $form['submit']->caption = 'Vytvořit';
        $form['submit']->setHtmlAttribute('class', 'btn btn-primary col-12');
        $form->onSuccess[] = [$this, 'handleFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->getValues();
        $validateFields['logo'] = new ValidatorArgument($values->logo, 'notEmpty');
        $validateFields['groups'] = new ValidatorArgument($values->groups, 'arrayNotEmpty');
        $validateFields['schoolYear'] = new ValidatorArgument($values->schoolYear, 'schoolYear');
        $validateFields['testNumber'] = new ValidatorArgument($values->testNumber, 'intNotNegative');
        $validateFields['testTerm'] = new ValidatorArgument($values->testTerm, 'notEmpty');
        $this->validator->validate($form, $validateFields);
        $this->redrawErrors();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleFormSuccess(Form $form, ArrayHash $values): void
    {
        try{
            $test = $this->testGeneratorService->generateTest($values);
        }
        catch(\Exception $e){
            $this->onError($e);
            return;
        }
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/templates/export.latte');
        try{
            $this->entityManager->flush();
        } catch (\Exception $e){
            $this->onError($e);
            return;
        }

        if($test){
            $template->test = $test;
            foreach($test->getTestVariants()->getValues() as $testVariant){
                $template->testVariant = $testVariant;
                FileSystem::createDir( DATA_DIR . '/tests/' . $test->getId());
                file_put_contents( DATA_DIR . '/tests/' . $test->getId() . '/variant_' . Strings::lower($testVariant->getLabel()) . '.tex', (string) $template);
            }
            $this->fileService->createTestZip($test);
            $this->onSuccess();
        }
    }

    /**
     * @param array $filters
     */
    public function handleFilterChange(array $filters): void
    {
        //bdump($filters);
        foreach($filters as $problemKey => $problemFilters){

            if(!isset($problemFilters['filters'])){
                $problemFilters['filters'] = [];
            }

            if(!isset($problemFilters['filters']['is_template']) || $problemFilters['filters']['is_template'] === ''){
                unset($problemFilters['filters']['is_template']);
                $filterRes = $this->problemRepository->findFiltered($problemFilters['filters']);
            }
            else if($problemFilters['filters']['is_template']){
                $filterRes = $this->problemTemplateRepository->findFiltered($problemFilters['filters']);
            }
            else{
                $filterRes = $this->problemFinalRepository->findFiltered($problemFilters['filters']);
            }

            if(isset($problemFilters['filters'])){
                foreach ($problemFilters['filters'] as $filterType => $filterVal) {
                    //bdump($filterVal);
                    $this['form'][$filterType . '_' . $problemKey]->setValue($filterVal);
                }
            }

            $this['form']['problem_' . $problemKey]->setItems($filterRes);

            //bdump($filterRes);

            $valuesToSetArr = [];
            $valuesToSetObj = [];

            if(isset($problemFilters['selected'])){
                foreach ($problemFilters['selected'] as $selected){
                    //bdump($selected);
                    if(array_key_exists((int) $selected, $filterRes)){
                        $valuesToSetArr[] = $selected;
                        $valuesToSetObj[$selected] = $filterRes[$selected];
                    }
                }
            }

            $this['form']['problem_' . $problemKey]->setValue($valuesToSetArr);
            $this['problemStack' . $problemKey]->setProblems($filterRes, $valuesToSetObj);

        }

        $this->redrawControl('testCreateFormSnippet');

    }

    public function render(): void
    {
        $this->template->maxProblems = $this->maxProblems;
        $this->template->problemConditionTypes = $this->problemConditionTypes;

        for ($i = 0; $i < $this->maxProblems; $i++){
            $this['problemStack' . $i]->template->id = $i;
        }

        $this->template->render(__DIR__ . '/templates/create.latte');
    }
}
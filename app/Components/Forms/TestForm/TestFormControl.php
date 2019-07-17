<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.5.19
 * Time: 17:39
 */

namespace App\Components\Forms\TestForm;


use App\Components\Forms\FormControl;
use App\Components\ProblemStack\IProblemStackFactory;
use App\Model\Entity\Logo;
use App\Model\Entity\Problem;
use App\Model\Entity\ProblemConditionType;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\LogoRepository;
use App\Model\Repository\ProblemConditionTypeRepository;
use App\Model\Repository\ProblemFinalRepository;
use App\Model\Repository\ProblemRepository;
use App\Model\Repository\ProblemTemplateRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TestRepository;
use App\Services\FileService;
use App\Services\TestBuilderService;
use App\Services\ValidationService;
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
     * @var TestBuilderService
     */
    protected $testBuilderService;

    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * @var IProblemStackFactory
     */
    protected $problemStackFactory;

    /**
     * @var Logo[]
     */
    protected $logos;

    /**
     * @var Problem[]
     */
    protected $problems;

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
     * @param ValidationService $validationService
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
     * @param TestBuilderService $testBuilderService
     * @param FileService $fileService
     * @param IProblemStackFactory $problemStackFactory
     * @throws \Exception
     */
    public function __construct
    (
        ValidationService $validationService, EntityManager $entityManager,
        TestRepository $testRepository,
        ProblemRepository $problemRepository, ProblemTemplateRepository $problemTemplateRepository, ProblemFinalRepository $problemFinalRepository,
        ProblemTypeRepository $problemTypeRepository,
        DifficultyRepository $difficultyRepository, LogoRepository $logoRepository, GroupRepository $groupRepository,
        SubCategoryRepository $subCategoryRepository,
        ProblemConditionTypeRepository $problemConditionTypeRepository,
        TestBuilderService $testBuilderService, FileService $fileService,
        IProblemStackFactory $problemStackFactory
    )
    {
        parent::__construct($validationService);

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
        $this->testBuilderService = $testBuilderService;
        $this->fileService = $fileService;
        $this->problemStackFactory = $problemStackFactory;

        $this->logos = $this->logoRepository->findAssoc([],'id');
        $this->problems = $this->problemRepository->findAssoc([], 'id');
        $this->problemConditionTypes = $this->problemConditionTypeRepository->findAssoc([], 'id');
    }

    /**
     * @param array $params
     * @throws \Nette\Application\BadRequestException
     */
    public function loadState(array $params): void
    {
        parent::loadState($params);

        $this->maxProblems = $this->presenter->context->parameters['testMaxProblems'];

        for ($i = 0; $i < $this->maxProblems; $i++){
            $this->addComponent($this->problemStackFactory->create(), 'problemStack' . $i);
        }
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

        $form->addSelect('logo', 'Logo *', $this->logos)
            ->setPrompt('Zvolte logo')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('test-logo');

        $form->addMultiSelect('groups', 'Skupiny *', $groups)
            ->setHtmlAttribute('class', 'form-control selectpicker')
            ->setHtmlAttribute('title', 'Zvolte skupiny');

        $form->addText('test_term', 'Období *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte období ve školním roce.');

        $form->addText('school_year', 'Školní rok *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'rrrr/rr(rr) nebo rrrr-rr(rr)');

        $form->addInteger('test_number', 'Číslo testu *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte číslo testu.');

        // Úvodní text se zobrazí pod hlavičkou testu
        $form->addTextArea('introduction_text', 'Úvodní text')
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

            $form->addMultiSelect('problem_'.$i, 'Zvolené úlohy', $this->problems)
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
        bdump($values);
        $validateFields['logo'] = $values->logo;
        $validateFields['groups'] = ArrayHash::from($values->groups);
        $validateFields['school_year'] = $values->school_year;
        $validateFields['test_number'] = $values->test_number;
        $validateFields['test_term'] = $values->test_term;
        $validationErrors = $this->validationService->validate($validateFields);
        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error){
                    $form[$veKey]->addError($error);
                }
            }
        }
        $this->redrawControl('logoErrorSnippet');
        $this->redrawControl('groupsErrorSnippet');
        $this->redrawControl('schoolYearErrorSnippet');
        $this->redrawControl('testNumberErrorSnippet');
        $this->redrawControl('testTermErrorSnippet');
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleFormSuccess(Form $form, ArrayHash $values): void
    {
        try{
            bdump('HANDLE FORM SUCCESS');
            $testData = $this->testBuilderService->buildTest($values);
        }
        catch(\Exception $e){
            $this->onError($e);
            bdump($e->getMessage());
            bdump('BUILD TEST ERROR');
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
        foreach($testData->variants as $variant){
            $template->variant = $variant;
            $template->test = $testData->test;
            FileSystem::createDir( DATA_DIR . '/tests/' . $testData->testId);
            file_put_contents( DATA_DIR . '/tests/' . $testData->testId . '/variant_' . Strings::lower($variant) . '.tex', (string) $template);
        }
        $this->fileService->createTestZip($testData->testId);
        $this->onSuccess();
    }

    /**
     * @param array $filters
     */
    public function handleFilterChange(array $filters): void
    {
        bdump($filters);
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
                    bdump($filterVal);
                    $this['form'][$filterType . '_' . $problemKey]->setValue($filterVal);
                }
            }

            $this['form']['problem_' . $problemKey]->setItems($filterRes);

            bdump($filterRes);

            $valuesToSetArr = [];
            $valuesToSetObj = [];

            if(isset($problemFilters['selected'])){
                foreach ($problemFilters['selected'] as $selected){
                    bdump($selected);
                    if(array_key_exists((int) $selected, $filterRes)){
                        $valuesToSetArr[] = $selected;
                        $valuesToSetObj[$selected] = $filterRes[$selected];
                    }
                }
            }

            bdump($valuesToSetArr);

            $this['form']['problem_' . $problemKey]->setValue($valuesToSetArr);
            $this['problemDragAndDrop' . $problemKey]->setProblems($filterRes, $valuesToSetObj);

        }

        $this->redrawControl('testCreateFormSnippet');

    }

    public function render(): void
    {
        $this->template->maxProblems = $this->maxProblems;
        $this->template->logos = $this->logos;
        $this->template->problemConditionTypes = $this->problemConditionTypes;

        for ($i = 0; $i < $this->maxProblems; $i++){
            $this['problemStack' . $i]->template->id = $i;
        }

        $this->template->render(__DIR__ . '/templates/create.latte');
    }
}
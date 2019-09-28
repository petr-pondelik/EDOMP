<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.9.19
 * Time: 11:02
 */

namespace App\Components\Forms\TestForm\TestEntityForm;

use App\Arguments\ValidatorArgument;
use App\Components\Forms\TestForm\TestFormControl;
use App\Components\LogoDragAndDrop\ILogoDragAndDropFactory;
use App\Components\LogoDragAndDrop\LogoDragAndDropControl;
use App\Components\LogoView\ILogoViewFactory;
use App\Components\ProblemStack\IProblemStackFactory;
use App\Exceptions\ComponentException;
use App\Model\Persistent\Entity\ProblemConditionType;
use App\Model\Persistent\Functionality\TestFunctionality;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\GroupRepository;
use App\Model\Persistent\Repository\LogoRepository;
use App\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\Model\Persistent\Repository\ProblemFinal\ProblemFinalRepository;
use App\Model\Persistent\Repository\ProblemRepository;
use App\Model\Persistent\Repository\ProblemTemplate\ProblemTemplateRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Services\FileService;
use App\Services\TestGeneratorService;
use App\Services\Validator;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;

/**
 * Class TestEntityFormControl
 * @package App\Components\Forms\TestForm\TestEntityForm
 */
class TestEntityFormControl extends TestFormControl
{
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
     * @var SubCategoryRepository
     */
    protected $subCategoryRepository;

    /**
     * @var ProblemConditionTypeRepository
     */
    protected $problemConditionTypeRepository;

    /**
     * @var ILogoDragAndDropFactory
     */
    protected $logoDragAndDropFactory;

    /**
     * @var IProblemStackFactory
     */
    protected $problemStackFactory;

    /**
     * @var ILogoViewFactory
     */
    protected $logoViewFactory;

    /**
     * @var ProblemConditionType[]
     */
    protected $problemConditionTypes;

    /**
     * @var int
     */
    protected $maxProblems;

    /**
     * TestEntityFormControl constructor.
     * @param Validator $validator
     * @param EntityManager $entityManager
     * @param LogoRepository $logoRepository
     * @param GroupRepository $groupRepository
     * @param TestGeneratorService $testGeneratorService
     * @param FileService $fileService
     * @param ProblemRepository $problemRepository
     * @param ProblemTemplateRepository $problemTemplateRepository
     * @param ProblemFinalRepository $problemFinalRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param DifficultyRepository $difficultyRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param ILogoDragAndDropFactory $logoDragAndDropFactory
     * @param IProblemStackFactory $problemStackFactory
     * @param ILogoViewFactory $logoViewFactory
     * @param TestFunctionality $testFunctionality
     * @throws \Exception
     */
    public function __construct
    (
        Validator $validator,
        EntityManager $entityManager,
        LogoRepository $logoRepository, GroupRepository $groupRepository,
        TestGeneratorService $testGeneratorService,
        FileService $fileService,
        ProblemRepository $problemRepository, ProblemTemplateRepository $problemTemplateRepository, ProblemFinalRepository $problemFinalRepository,
        ProblemTypeRepository $problemTypeRepository, DifficultyRepository $difficultyRepository, SubCategoryRepository $subCategoryRepository,
        ProblemConditionTypeRepository $problemConditionTypeRepository,
        ILogoDragAndDropFactory $logoDragAndDropFactory, IProblemStackFactory $problemStackFactory, ILogoViewFactory $logoViewFactory,
        TestFunctionality $testFunctionality
    )
    {
        parent::__construct($validator, $entityManager, $logoRepository, $groupRepository, $testGeneratorService, $fileService, $testFunctionality);
        $this->problemRepository = $problemRepository;
        $this->problemTemplateRepository = $problemTemplateRepository;
        $this->problemFinalRepository = $problemFinalRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->difficultyRepository = $difficultyRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->problemConditionTypeRepository = $problemConditionTypeRepository;
        $this->logoDragAndDropFactory = $logoDragAndDropFactory;
        $this->problemStackFactory = $problemStackFactory;
        $this->logoViewFactory = $logoViewFactory;
        $this->problemConditionTypes = $this->problemConditionTypeRepository->findAssoc([], 'id');
    }

    /**
     * @param array $params
     * @throws \Nette\Application\BadRequestException
     */
    public function loadState(array $params): void
    {
        parent::loadState($params);

        if (!$this->isUpdate()) {
            $this->maxProblems = $this->presenter->context->parameters['testMaxProblems'];
            $problems = $this->problemRepository->findAssoc(['isGenerated' => false], 'id');

            for ($i = 0; $i < $this->maxProblems; $i++) {
                $this->addComponent($this->problemStackFactory->create($i), 'problemStack' . $i);
            }

            for ($i = 0; $i < $this->maxProblems; $i++) {
                $this['problemStack' . $i]->setProblems($problems);
            }
        } else {
            $this->addComponent($this->logoViewFactory->create(), 'logoView');
        }
    }

    public function initComponents(): void
    {
        parent::initComponents();
        if ($this->isUpdate()) {
            $this['logoView']->setLogo($this->entity->getLogo());
        }
    }

    /**
     * @return LogoDragAndDropControl
     */
    public function createComponentLogoDragAndDrop(): LogoDragAndDropControl
    {
        return $this->logoDragAndDropFactory->create();
    }

    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        if (!$this->isUpdate()) {
            $form = $this->prepareCreateForm($form);
        } else {
            $form = $this->prepareUpdateForm($form);
        }

        return $form;
    }

    /**
     * @param Form $form
     * @return Form
     * @throws ComponentException
     * @throws \Nette\Utils\JsonException
     */
    public function prepareCreateForm(Form $form): Form
    {
        if ($this->isUpdate()) {
            throw new ComponentException('Trying to prepare create form in update action.');
        }

        $difficulties = $this->difficultyRepository->findAssoc([], 'id');
        $subCategories = $this->subCategoryRepository->findAssoc([], 'id');
        $problems = $this->problemRepository->findAssoc([], 'id');
        $problemTypes = $this->problemTypeRepository->findAssoc([], 'id');

        $conditionTypesByProblemTypes = [];
        foreach ($problemTypes as $id => $problemType) {
            foreach ($problemType->getConditionTypes()->getValues() as $conditionType) {
                $conditionTypesByProblemTypes[$id] = $conditionType->getId();
            }
        }

        for ($i = 0; $i < $this->maxProblems; $i++) {

            $form->addSelect('is_template_' . $i, 'Šablona', [
                1 => 'Ano',
                0 => 'Ne'
            ])
                ->setPrompt('Zvolte')
                ->setHtmlAttribute('class', 'form-control filter selectpicker')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'is_template')
                ->setHtmlId('is_template_' . $i);

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
                ->setHtmlAttribute('data-condition-types', Json::encode($conditionTypesByProblemTypes))
                ->setHtmlAttribute('title', 'Zvolte typy')
                ->setHtmlId('problem_type_id_' . $i);

            $form->addMultiSelect('difficulty_id_' . $i, 'Obtížnost', $difficulties)
                ->setHtmlAttribute('class', 'form-control filter selectpicker')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'difficulty_id')
                ->setHtmlAttribute('title', 'Zvolte obtížnosti')
                ->setHtmlId('difficulty_id_' . $i);

            foreach ($this->problemConditionTypes as $conditionType) {

                $form->addMultiSelect('condition_type_id_' . $conditionType->getId() . '_' . $i, $conditionType->getLabel(),
                    $conditionType->getProblemConditions()->getValues()
                )
                    ->setHtmlAttribute('class', 'form-control filter selectpicker')
                    ->setHtmlAttribute('data-problem-id', $i)
                    ->setHtmlAttribute('data-filter-type', 'condition_type_id_' . $conditionType->getId())
                    ->setHtmlAttribute('title', $conditionType->getPrompt());

            }

            $form->addMultiSelect('problem_' . $i, 'Zvolené úlohy', $problems)
                ->setHtmlAttribute('class', 'form-control filter problem-select')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlId('problem_' . $i);

            $form->addCheckbox('newpage_' . $i, 'Nová stránka');

        }

        $form['submit']->caption = 'Vytvořit';
        $form['submit']->setHtmlAttribute('class', 'btn btn-primary col-12');

        return $form;
    }

    /**
     * @param Form $form
     * @return Form
     * @throws ComponentException
     */
    public function prepareUpdateForm(Form $form): Form
    {
        if (!$this->isUpdate()) {
            throw new ComponentException('Trying to prepare update form outside of update action.');
        }

        $form['variant']->setDisabled();

        for ($i = 0; $i < $this->entity->getVariantsCnt(); $i++) {
            for ($j = 0; $j < $this->entity->getProblemsPerVariant(); $j++) {
                $form->addInteger('problem_final_id_disabled_' . $i . '_' . $j, 'ID příkladu')
                    ->setHtmlAttribute('class', 'form-control')
                    ->setDisabled();
                $form->addHidden('problem_final_id_' . $i . '_' . $j);
                $form->addInteger('problem_template_id_disabled_' . $i . '_' . $j, 'ID šablony')
                    ->setHtmlAttribute('class', 'form-control')
                    ->setDisabled();
                $form->addHidden('problem_template_id_' . $i . '_' . $j);
                $form->addText('success_rate_' . $i . '_' . $j, 'Úspěšnost v testu')
                    ->setHtmlAttribute('class', 'form-control')
                    ->setHtmlAttribute('placeholder', 'Zadejte desetinné číslo v intervalu <0; 1>');
            }
        }

        $form->onValidate = null;
        $form->onValidate[] = [$this, 'handleUpdateFormValidate'];

        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleUpdateFormValidate(Form $form): void
    {
        bdump('HANDLE UPDATE FORM VALIDATE');
        $values = $form->getValues();
        for ($i = 0; $i < $this->entity->getVariantsCnt(); $i++) {
            for ($j = 0; $j < $this->entity->getProblemsPerVariant(); $j++) {
                $validateFields['success_rate'] = new ValidatorArgument($values->{'success_rate_' . $i . '_' . $j}, 'range0to1', 'success_rate_' . $i . '_' . $j);
                $this->validator->validate($form, $validateFields);
            }
        }
        if(!$this->entity->isClosed()){
            $this->handleFormValidate($form);
        }
        $this->redrawControl('formSnippetArea');
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Exception
     */
    public function handleFormSuccess(Form $form, ArrayHash $values): void
    {
        try {
            $this->testGeneratorService->generateTest($values);
        } catch (\Exception $e) {
            bdump($e);
            $this->onError($e);
            return;
        }
        $this->onSuccess();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Exception
     */
    public function handleEditFormSuccess(Form $form, ArrayHash $values): void
    {
        bdump('HANDLE EDIT FORM SUCCESS');
        bdump($values);

        try {
            $values->updateBasics = !$this->entity->isClosed();
            $values->updateStatistics = true;
            $this->functionality->update($this->entity->getId(), $values);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            bdump($e);
            $this->onError($e);
            return;
        }

        $this->onSuccess();
    }

    /**
     * @param array $filters
     */
    public function handleFilterChange(array $filters): void
    {
        foreach ($filters as $problemKey => $problemFilters) {

            if (!isset($problemFilters['filters'])) {
                $problemFilters['filters'] = [];
            }

            // Pick only non-generated problems
            $problemFilters['filters']['is_generated'] = false;

            if (!isset($problemFilters['filters']['is_template']) || $problemFilters['filters']['is_template'] === '') {
                unset($problemFilters['filters']['is_template']);
                $filterRes = $this->problemRepository->findFiltered($problemFilters['filters']);
            } else if ($problemFilters['filters']['is_template']) {
                $filterRes = $this->problemTemplateRepository->findFiltered($problemFilters['filters']);
            } else {
                $filterRes = $this->problemFinalRepository->findFiltered($problemFilters['filters']);
            }

            if (isset($problemFilters['filters'])) {
                foreach ($problemFilters['filters'] as $filterType => $filterVal) {
                    if ($filterType !== 'is_generated') {
                        $this['form'][$filterType . '_' . $problemKey]->setValue($filterVal);
                    }
                }
            }

            $this['form']['problem_' . $problemKey]->setItems($filterRes);

            $valuesToSetArr = [];
            $valuesToSetObj = [];

            if (isset($problemFilters['selected'])) {
                foreach ($problemFilters['selected'] as $selected) {
                    if (array_key_exists((int)$selected, $filterRes)) {
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

    public function setDefaults(): void
    {
        $this['form']->setDefaults([
            'variant' => $this->entity->getVariantsCnt(),
            'problemsCnt' => $this->entity->getProblemsPerVariant(),
            'groups' => $this->entity->getPropertyKeyArray('groups'),
            'testTerm' => $this->entity->getTerm(),
            'testNumber' => $this->entity->getTestNumber(),
            'schoolYear' => $this->entity->getSchoolYear(),
            'logo' => $this->entity->getLogo()->getId(),
            'introductionText' => $this->entity->getIntroductionText()
        ]);

        $testVariants = $this->entity->getTestVariants()->getValues();

        foreach ($testVariants as $i => $testVariant) {
            $problemFinalAssociations = $testVariant->getProblemFinalAssociations()->getValues();
            foreach ($problemFinalAssociations as $j => $problemFinalAssociation) {
                $problemFinal = $problemFinalAssociation->getProblemFinal();
                $this['form']->setDefaults([
                    'problem_final_id_disabled_' . $i . '_' . $j => $problemFinal->getId(),
                    'problem_final_id_' . $i . '_' . $j => $problemFinal->getId(),
                    'success_rate_' . $i . '_' . $j => $problemFinalAssociation->getSuccessRate()
                ]);
                if ($problemTemplate = $problemFinal->getProblemTemplate()) {
                    $this['form']->setDefaults([
                        'problem_template_id_disabled_' . $i . '_' . $j => $problemTemplate->getId(),
                        'problem_template_id_' . $i . '_' . $j => $problemTemplate->getId()
                    ]);
                }
            }
        }
    }

    public function render(): void
    {
        bdump('TEST ENTITY FORM RENDER');
        if (!$this->isUpdate()) {
            $this->template->maxProblems = $this->maxProblems;
            $this->template->problemConditionTypes = $this->problemConditionTypes;
        }
        parent::render();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.9.19
 * Time: 11:02
 */

namespace App\TeacherModule\Components\Forms\TestForm;

use App\CoreModule\Arguments\ValidatorArgument;
use App\TeacherModule\Components\FilterView\IFilterViewFactory;
use App\CoreModule\Components\Forms\EntityFormControl;
use App\CoreModule\Exceptions\ComponentException;
use App\CoreModule\Model\Persistent\Entity\ProblemConditionType;
use App\CoreModule\Model\Persistent\Functionality\TestFunctionality;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;
use App\CoreModule\Model\Persistent\Repository\GroupRepository;
use App\CoreModule\Model\Persistent\Repository\LogoRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemFinal\ProblemFinalRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\ProblemTemplateRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use App\CoreModule\Model\Persistent\Repository\SubCategoryRepository;
use App\CoreModule\Services\FileService;
use App\TeacherModule\Components\LogoDragAndDrop\ILogoDragAndDropFactory;
use App\TeacherModule\Components\LogoDragAndDrop\LogoDragAndDropControl;
use App\TeacherModule\Components\LogoView\ILogoViewFactory;
use App\TeacherModule\Components\ProblemStack\IProblemStackFactory;
use App\TeacherModule\Services\FilterSession;
use App\TeacherModule\Services\TestGenerator;
use App\CoreModule\Services\Validator;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;
use Nette\Utils\Strings;
use IPub\VisualPaginator\Components as VisualPaginator;


/**
 * Class TestFormControl
 * @package App\TeacherModule\Components\Forms\TestForm
 */
class TestFormControl extends EntityFormControl
{
    /**
     * @var LogoRepository
     */
    protected $logoRepository;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @var TestGenerator
     */
    protected $testGenerator;

    /**
     * @var FileService
     */
    protected $fileService;

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
     * @var IFilterViewFactory
     */
    protected $filterViewFactory;

    /**
     * @var FilterSession
     */
    protected $filterSession;

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
     * @param ConstraintEntityManager $entityManager
     * @param LogoRepository $logoRepository
     * @param GroupRepository $groupRepository
     * @param TestGenerator $testGenerator
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
     * @param IFilterViewFactory $filterViewFactory
     * @param TestFunctionality $testFunctionality
     * @param FilterSession $filterSession
     * @throws \Exception
     */
    public function __construct
    (
        Validator $validator,
        ConstraintEntityManager $entityManager,
        LogoRepository $logoRepository,
        GroupRepository $groupRepository,
        TestGenerator $testGenerator,
        FileService $fileService,
        ProblemRepository $problemRepository,
        ProblemTemplateRepository $problemTemplateRepository,
        ProblemFinalRepository $problemFinalRepository,
        ProblemTypeRepository $problemTypeRepository,
        DifficultyRepository $difficultyRepository,
        SubCategoryRepository $subCategoryRepository,
        ProblemConditionTypeRepository $problemConditionTypeRepository,
        ILogoDragAndDropFactory $logoDragAndDropFactory,
        IProblemStackFactory $problemStackFactory,
        ILogoViewFactory $logoViewFactory,
        IFilterViewFactory $filterViewFactory,
        TestFunctionality $testFunctionality,
        FilterSession $filterSession
    )
    {
        parent::__construct($validator, $entityManager);
        $this->logoRepository = $logoRepository;
        $this->groupRepository = $groupRepository;
        $this->testGenerator = $testGenerator;
        $this->fileService = $fileService;
        $this->functionality = $testFunctionality;
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
        $this->filterViewFactory = $filterViewFactory;
        $this->filterSession = $filterSession;
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
        if (!$this->presenter->isAjax()) {
            $this->filterSession->erase();
        }
    }

    /**
     * @return bool
     */
    public function isRegenerate(): bool
    {
        return $this->getAction() === 'regenerate';
    }

    /**
     * @param iterable|null $args
     */
    public function initComponents(iterable $args = null): void
    {
        if (!$this->isCreate()) {
            $this->addComponent($this->logoViewFactory->create(), 'logoView');
        }

        if ($this->isCreate()) {
            for ($i = 0; $i < $this->maxProblems; $i++) {
                $this->addPaginator($i);
                $this->addComponent($this->problemStackFactory->create($i), 'problemStack' . $i);
            }
        }

        if ($this->isRegenerate()) {
            for ($i = 0; $i < $this->entity->getProblemsPerVariant(); $i++) {
                $this->addComponent($this->filterViewFactory->create(), 'filterView' . $i);
            }
        }
    }

    /**
     * @param iterable|null $args
     */
    public function fillComponents(iterable $args = null): void
    {
        if ($this->isCreate()) {

            $problems = $this->problemRepository->findFiltered([
                'isGenerated' => false,
                'createdBy' => $this->presenter->user->id
            ]);

            $problemsCnt = count($problems);

            for ($i = 0; $i < $this->maxProblems; $i++) {

                if (!isset($args[$i])) {

                    // Problem stack paginator
                    $paginator = $this['paginator' . $i]->getPaginator();
                    $paginator->itemsPerPage = 10;
                    $paginator->itemCount = $problemsCnt;

                    $problemsInterval = array_slice($problems, $paginator->offset, $paginator->itemsPerPage);

                    bdump($problemsInterval);

                    $this['problemStack' . $i]->setProblems($problemsInterval);

                }

            }

        }

        if (!$this->isCreate()) {
            $this['logoView']->setLogo($this->entity->getLogo());
        }

        if ($this->isRegenerate()) {
            $persistedFilters = $this->entity->getFilters()->getValues();
            foreach ($persistedFilters as $persistedFilter) {
                $this['filterView' . $persistedFilter->getSeq()]->setEntity($persistedFilter);
            }
        }
    }

    /**
     * @param int $id
     */
    public function addPaginator(int $id): void
    {
        $paginatorControl = new VisualPaginator\Control();
        $paginatorControl->enableAjax();
        $paginatorControl->setTemplateFile(TEACHER_MODULE_TEMPLATES_DIR . '/VisualPaginator/problemStack.latte');

        $paginatorControl->onShowPage[] = function ($filters) use ($id) {
            bdump('ON SHOW PAGE');
            bdump($filters);
            bdump($this->filterSession->getFilters());
            $this->handleFilterChange($id, $this->filterSession->getFilters());
        };

        $this->addComponent($paginatorControl, 'paginator' . $id);
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
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $groups = $this->groupRepository->findAllowed($this->presenter->user);
        $logos = $this->logoRepository->findAssoc([], 'id');

        $form->addSelect('variantsCnt', 'Počet variant *', [
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

        $form->addHidden('problemsPerVariant')->setDefaultValue(1)
            ->setHtmlId('problemsPerVariant');

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

        $form = $this->{'prepare' . Strings::firstUpper($this->getAction()) . 'Form'}($form);

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
        if (!$this->isCreate()) {
            throw new ComponentException('Trying to prepare create form outside create action.');
        }

        $difficulties = $this->difficultyRepository->findAssoc([], 'id');
        $subCategories = $this->subCategoryRepository->findAssoc([], 'id');
        $problemTypes = $this->problemTypeRepository->findAssoc([], 'id');

        $conditionTypesByProblemTypes = [];
        foreach ($problemTypes as $id => $problemType) {
            foreach ($problemType->getConditionTypes()->getValues() as $conditionType) {
                if (!$conditionType->isValidation()) {
                    $conditionTypesByProblemTypes[$id] = $conditionType->getId();
                }
            }
        }

        for ($i = 0; $i < $this->maxProblems; $i++) {

            $form->addSelect('isTemplate' . $i, 'Šablona', [
                1 => 'Ano',
                0 => 'Ne'
            ])
                ->setPrompt('Zvolte')
                ->setHtmlAttribute('class', 'form-control filter selectpicker')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'isTemplate')
                ->setHtmlId('is_template_' . $i);

            $form->addMultiSelect('subCategory' . $i, 'Téma', $subCategories)
                ->setHtmlAttribute('class', 'form-control filter selectpicker')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'subCategory')
                ->setHtmlAttribute('title', 'Zvolte témata')
                ->setHtmlId('sub_category_' . $i);

            $form->addMultiSelect('problemType' . $i, 'Typ', $problemTypes)
                ->setHtmlAttribute('class', 'form-control filter problem-type-filter selectpicker')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'problemType')
                ->setHtmlAttribute('data-condition-types', Json::encode($conditionTypesByProblemTypes))
                ->setHtmlAttribute('title', 'Zvolte typy')
                ->setHtmlId('problem_type_' . $i);

            $form->addMultiSelect('difficulty' . $i, 'Obtížnost', $difficulties)
                ->setHtmlAttribute('class', 'form-control filter selectpicker')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'difficulty')
                ->setHtmlAttribute('title', 'Zvolte obtížnosti')
                ->setHtmlId('difficulty_' . $i);

            foreach ($this->problemConditionTypes as $conditionType) {

                $form->addMultiSelect('conditionType' . $conditionType->getId() . $i, $conditionType->getLabel(),
                    $conditionType->getProblemConditions()->getValues()
                )
                    ->setHtmlAttribute('class', 'form-control filter selectpicker')
                    ->setHtmlAttribute('data-problem-id', $i)
                    ->setHtmlAttribute('data-filter-type', 'conditionType')
                    ->setHtmlAttribute('data-filter-type-secondary', $conditionType->getId())
                    ->setHtmlAttribute('title', $conditionType->getPrompt());

            }

            $form->addTextArea('problem' . $i, 'Zvolené úlohy')
                // hidden
                ->setHtmlAttribute('class', 'form-control filter selected-problems')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'selected')
                ->setHtmlId('problem-' . $i);

            $form->addCheckbox('newPage' . $i, 'Konec strany zadání');

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

        $form['variantsCnt']->setDisabled();

        for ($i = 0; $i < $this->entity->getVariantsCnt(); $i++) {
            for ($j = 0; $j < $this->entity->getProblemsPerVariant(); $j++) {
                $form->addInteger('problemFinalIdDisabled' . $i . $j, 'ID příkladu')
                    ->setHtmlAttribute('class', 'form-control')
                    ->setDisabled();
                $form->addHidden('problemFinalId' . $i . $j);
                $form->addInteger('problemTemplateIdDisabled' . $i . $j, 'ID šablony')
                    ->setHtmlAttribute('class', 'form-control')
                    ->setDisabled();
                $form->addHidden('problemTemplateId' . $i . $j);
                $form->addText('successRate' . $i . $j, 'Úspěšnost v testu')
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
     * @return Form
     * @throws ComponentException
     */
    public function prepareRegenerateForm(Form $form): Form
    {
        if (!$this->isRegenerate()) {
            throw new ComponentException('Trying to prepare regenerate form outside of regenerate action.');
        }

        $form['variantsCnt']->setDisabled();

        for ($i = 0; $i < $this->entity->getProblemsPerVariant(); $i++) {
            $form->addSelect('regenerateProblem' . $i, 'Přegenerovat úlohu', [
                0 => 'Ne',
                1 => 'Ano'
            ])
                ->setHtmlAttribute('class', 'form-control');
        }

        $form->onSuccess = null;
        $form->onSuccess[] = [$this, 'handleRegenerateFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @throws \App\CoreModule\Exceptions\ValidatorException
     */
    public function handleFormValidate(Form $form): void
    {
        bdump('HANDLE FORM VALIDATE');
        $values = $form->getValues();
        bdump($values);
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
     * @throws \App\CoreModule\Exceptions\ValidatorException
     */
    public function handleUpdateFormValidate(Form $form): void
    {
        bdump('HANDLE UPDATE FORM VALIDATE');
        $values = $form->getValues();
        for ($i = 0; $i < $this->entity->getVariantsCnt(); $i++) {
            for ($j = 0; $j < $this->entity->getProblemsPerVariant(); $j++) {
                $validateFields['successRate'] = new ValidatorArgument($values->{'successRate' . $i . $j}, 'range0to1', 'successRate' . $i . $j);
                $this->validator->validate($form, $validateFields);
            }
        }
        if (!$this->entity->isClosed()) {
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
        bdump($values);
        bdump($this->filterSession->getFilters());
        $values->userId = $this->presenter->user->id;
        try {
            $this->testGenerator->generateTest($values);
        } catch (\Exception $e) {
            bdump($e);
            $this->onError($e);
            return;
        }
//        $this->onSuccess();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Exception
     */
    public function handleEditFormSuccess(Form $form, ArrayHash $values): void
    {
        bdump('HANDLE EDIT FORM SUCCESS');
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
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleRegenerateFormSuccess(Form $form, ArrayHash $values): void
    {
        bdump('HANDLE REGENERATE FORM SUCCESS');
        try {
            $this->testGenerator->regenerateTest($this->entity->getId(), $values);
        } catch (\Exception $e) {
            bdump($e);
            $this->onError($e);
            return;
        }
        $this->onSuccess();
    }

    /**
     * @param int $key
     * @param array $filters
     * @throws \Nette\Utils\JsonException
     */
    public function handleFilterChange(int $key, array $filters): void
    {
        bdump('HANDLE FILTER CHANGE');
        bdump($filters);

        bdump($key);

        if (!isset($filters[$key])) {
            $filters[$key] = [];
        }

        $resFilters = [];
        $problemFilters = $filters[$key];

        if (!isset($problemFilters['filters'])) {
            $problemFilters['filters'] = [];
        }

        if (!isset($problemFilters['selected'])) {
            $problemFilters['selected'] = [];
        }

        // Pick only non-generated problems
        $problemFilters['filters']['isGenerated'] = false;
        $problemFilters['filters']['createdBy'] = $this->presenter->user->id;

        // If selected is in JSON string format, decode it
        if ($problemFilters['selected'] && !is_array($problemFilters['selected'])) {
            $problemFilters['selected'] = Json::decode($problemFilters['selected'], Json::FORCE_ARRAY);
        }

        $filterRes = $this->problemRepository->findFiltered($problemFilters['filters']);
        bdump('FILTER RES');
        bdump($filterRes);

        $resFilters[$key] = $problemFilters;

        $this['form']['problem' . $key]->setValue(Json::encode($filterRes));

        $valuesToSetArr = [];
        $valuesToSetObj = [];

        bdump('PROBLEMS SELECTED');
        bdump($problemFilters['selected']);

        if (isset($problemFilters['selected']) && $problemFilters['selected']) {
            foreach ($problemFilters['selected'] as $selected) {
                if (array_key_exists((int)$selected, $filterRes)) {
                    $valuesToSetArr[] = $selected;
                    $valuesToSetObj[$selected] = $filterRes[$selected];
                    unset($filterRes[$selected]);
                }
            }
        }

        bdump('VALUES TO SET ARR');
        bdump($valuesToSetArr);
        $resFilters[$key]['selected'] = $valuesToSetArr;
        $this['form']['problem' . $key]->setValue(Json::encode($valuesToSetArr));

        $paginator = $this['paginator' . $key]->getPaginator();
        $paginator->itemCount = count($filterRes);
        $paginator->itemsPerPage = 10;

        bdump('FILTER RES');
        bdump($filterRes);

        $filterRes = array_slice($filterRes, $paginator->offset, $paginator->itemsPerPage);

        $this['problemStack' . $key]->setProblems($filterRes, $valuesToSetObj);

        bdump($resFilters);
        $this->filterSession->setFilters($resFilters);

        $this->presenter->payload->selected = [
            'key' => $key,
            'values' => $valuesToSetArr
        ];

        bdump('REDRAW');

        $this['problemStack' . $key]->redrawControl();
        $this['paginator' . $key]->redrawControl();

    }

    public function setDefaults(): void
    {
        $this['form']->setDefaults([
            'variantsCnt' => $this->entity->getVariantsCnt(),
            'problemsPerVariant' => $this->entity->getProblemsPerVariant(),
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
                    'problemFinalIdDisabled' . $i . $j => $problemFinal->getId(),
                    'problemFinalId' . $i . $j => $problemFinal->getId(),
                    'successRate' . $i . $j => $problemFinalAssociation->getSuccessRate()
                ]);
                if ($problemTemplate = $problemFinal->getProblemTemplate()) {
                    $this['form']->setDefaults([
                        'problemTemplateIdDisabled' . $i . $j => $problemTemplate->getId(),
                        'problemTemplateId' . $i . $j => $problemTemplate->getId()
                    ]);
                }
            }
        }
    }

    public function render(): void
    {
        bdump('TEST ENTITY FORM RENDER');
        bdump($this->getComponents());
        bdump($this->filterSession->getFilters());
        if ($this->isCreate()) {
            $this->template->maxProblems = $this->maxProblems;
            $this->template->problemConditionTypes = $this->problemConditionTypes;
        }
        parent::render();
    }
}
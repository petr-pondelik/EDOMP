<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.9.19
 * Time: 11:02
 */

namespace App\TeacherModule\Components\Forms\TestForm;

use App\CoreModule\Arguments\ValidatorArgument;
use App\CoreModule\Components\Paginator\PaginatorFactory;
use App\CoreModule\Model\Persistent\Entity\Logo;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionRepository;
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
use App\CoreModule\Model\Persistent\Repository\ProblemFinalRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\ProblemTemplateRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;
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
     * @var SubThemeRepository
     */
    protected $subThemeRepository;

    /**
     * @var ProblemConditionTypeRepository
     */
    protected $problemConditionTypeRepository;

    /**
     * @var ProblemConditionRepository
     */
    protected $problemConditionRepository;

    /**
     * @var ILogoDragAndDropFactory
     */
    protected $logoDragAndDropFactory;

    /**
     * @var IProblemStackFactory
     */
    protected $problemStackFactory;

    /**
     * @var PaginatorFactory
     */
    private $paginatorFactory;

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
     * @var Logo[]
     */
    protected $logos;

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
     * @param SubThemeRepository $subThemeRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
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
        SubThemeRepository $subThemeRepository,
        ProblemConditionTypeRepository $problemConditionTypeRepository,
        ProblemConditionRepository $problemConditionRepository,
        ILogoDragAndDropFactory $logoDragAndDropFactory,
        IProblemStackFactory $problemStackFactory,
        PaginatorFactory $paginatorFactory,
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
        $this->subThemeRepository = $subThemeRepository;
        $this->problemConditionTypeRepository = $problemConditionTypeRepository;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->logoDragAndDropFactory = $logoDragAndDropFactory;
        $this->problemStackFactory = $problemStackFactory;
        $this->paginatorFactory = $paginatorFactory;
        $this->logoViewFactory = $logoViewFactory;
        $this->filterViewFactory = $filterViewFactory;
        $this->filterSession = $filterSession;

        $this->problemConditionTypes = $this->problemConditionTypeRepository->findAssoc([], 'id');
    }

    /**
     * @param array $params
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \Nette\Application\BadRequestException
     */
    public function loadState(array $params): void
    {
        parent::loadState($params);
        $this->maxProblems = $this->presenter->context->parameters['testMaxProblems'];
        if (!$this->presenter->isAjax()) {
            $this->filterSession->erase();
        }
        $this->logos = $this->logoRepository->findAllowed($this->presenter->user);
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

            $filters = [ 'isGenerated' => false ];
            if (!$this->presenter->user->isInRole('admin')) {
                $filters['createdBy'] = $this->presenter->user->id;
            }

            $problems = $this->problemRepository->findFiltered($filters);
            $problemsCnt = count($problems);

            for ($i = 0; $i < $this->maxProblems; $i++) {
                if (!isset($args[$i])) {
                    // Problem stack paginator
                    $paginator = $this['paginator' . $i]->getPaginator();
                    $paginator->itemsPerPage = 10;
                    $paginator->itemCount = $problemsCnt;

                    $problemsInterval = array_slice($problems, $paginator->offset, $paginator->itemsPerPage);

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
        $paginatorControl = $this->paginatorFactory->create($this->paginatorFactory::TEACHER, 'problemStack.latte');
        $paginatorControl->onShowPage[] = function ($filters) use ($id) {
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

        $form->addSelect('variantsCnt', 'Po??et variant *', [
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

        $form->addSelect('logo', 'Zvolen?? logo *', $this->logos)
            ->setPrompt('Zvolte logo')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('test-logo');

        $form->addMultiSelect('groups', 'Skupiny *', $groups)
            ->setHtmlAttribute('class', 'form-control selectpicker')
            ->setHtmlAttribute('title', 'Zvolte skupiny');

        $form->addText('term', 'Obdob?? *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte obdob?? ve ??koln??m roce.');

        $form->addText('schoolYear', '??koln?? rok *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'rrrr/rr(rr) nebo rrrr-rr(rr)');

        $form->addInteger('testNumber', '????slo testu *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte ????slo testu.')
            ->setHtmlAttribute('min', 0)
            ->setHtmlAttribute('max', 10000);

        // ??vodn?? text se zobraz?? pod hlavi??kou testu
        $form->addTextArea('introductionText', '??vodn?? text')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte ??vodn?? text testu.');

        $form = $this->{'prepare' . Strings::firstUpper($this->getAction()) . 'Form'}($form);

        return $form;
    }

    /**
     * @param Form $form
     * @return Form
     * @throws ComponentException
     * @throws \Nette\Utils\JsonException
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function prepareCreateForm(Form $form): Form
    {
        if (!$this->isCreate()) {
            throw new ComponentException('Trying to prepare create form outside create action.');
        }

        $difficulties = $this->difficultyRepository->findAssoc([], 'id');
        $subThemes = $this->subThemeRepository->findAllowed($this->presenter->user);
        $problemTypes = $this->problemTypeRepository->findAssoc([], 'id');
        $conditionTypesByProblemTypes = $this->problemConditionTypeRepository->findIdAssocByProblemTypes();

        for ($i = 0; $i < $this->maxProblems; $i++) {

            $form->addSelect('isTemplate' . $i, 'Volit ??lohy z', [
                1 => '??ablon ??loh',
                0 => 'Fin??ln??ch ??loh'
            ])
                ->setPrompt('??ablon i fin??ln??ch ??loh')
                ->setHtmlAttribute('class', 'form-control filter selectpicker')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'isTemplate')
                ->setHtmlId('is_template_' . $i);

            $form->addMultiSelect('subTheme' . $i, 'T??mata ??loh', $subThemes)
                ->setHtmlAttribute('class', 'form-control filter selectpicker')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'subTheme')
                ->setHtmlAttribute('title', 'Volit ze v??ech')
                ->setHtmlId('sub_theme_' . $i);

            $form->addMultiSelect('problemType' . $i, 'Typy ??loh', $problemTypes)
                ->setHtmlAttribute('class', 'form-control filter problem-type-filter selectpicker')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'problemType')
                ->setHtmlAttribute('data-condition-types', Json::encode($conditionTypesByProblemTypes))
                ->setHtmlAttribute('title', 'Volit ze v??ech')
                ->setHtmlId('problem_type_' . $i);

            $form->addMultiSelect('difficulty' . $i, 'Obt????nosti', $difficulties)
                ->setHtmlAttribute('class', 'form-control filter selectpicker')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'difficulty')
                ->setHtmlAttribute('title', 'Volit ze v??ech')
                ->setHtmlId('difficulty_' . $i);

            foreach ($this->problemConditionTypes as $conditionType) {
                $form->addMultiSelect('conditionType' . $conditionType->getId() . $i, $conditionType->getLabel(),
                    $conditionType->getProblemConditions()->getValues()
                )
                    ->setHtmlAttribute('class', 'form-control filter selectpicker')
                    ->setHtmlAttribute('data-problem-id', $i)
                    ->setHtmlAttribute('data-filter-type', 'conditionType')
                    ->setHtmlAttribute('data-filter-type-secondary', $conditionType->getId())
                    ->setHtmlAttribute('title', 'Volit ze v??ech');
            }

            $form->addTextArea('problem' . $i, 'Zvolen?? ??lohy')
                ->setHtmlAttribute('class', 'form-control filter selected-problems hidden')
                ->setHtmlAttribute('data-problem-id', $i)
                ->setHtmlAttribute('data-filter-type', 'selected')
                ->setHtmlId('problem-' . $i);

            $form->addCheckbox('newPage' . $i, 'Konec strany zad??n??');

        }

        $form['submit']->caption = 'Vytvo??it';
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
                $form->addInteger('problemFinalIdDisabled' . $i . $j, 'ID p????kladu')
                    ->setHtmlAttribute('class', 'form-control')
                    ->setDisabled();
                $form->addHidden('problemFinalId' . $i . $j);
                $form->addInteger('problemTemplateIdDisabled' . $i . $j, 'ID ??ablony')
                    ->setHtmlAttribute('class', 'form-control')
                    ->setDisabled();
                $form->addHidden('problemTemplateId' . $i . $j);
                $form->addText('successRate' . $i . $j, '??sp????nost v testu')
                    ->setHtmlAttribute('class', 'form-control')
                    ->setHtmlAttribute('placeholder', 'Zadejte desetinn?? ????slo v intervalu <0; 1>');
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
            $form->addSelect('regenerateProblem' . $i, 'P??egenerovat ??lohu', [
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
        $validateFields['logo'] = new ValidatorArgument($values->logo, 'notEmpty');
        $validateFields['groups'] = new ValidatorArgument($values->groups, 'arrayNotEmpty');
        $validateFields['schoolYear'] = new ValidatorArgument($values->schoolYear, 'schoolYear');
        $validateFields['testNumber'] = new ValidatorArgument($values->testNumber, 'intNotNegative');
        $validateFields['term'] = new ValidatorArgument($values->term, 'notEmpty');
        $this->validator->validate($form, $validateFields);

        $firstErrorComponent = $this->getFirstErrorComponent($form);
        if ($firstErrorComponent) {
            $this->setFormErrorPayload($firstErrorComponent->getName());
        }

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

        $firstErrorComponent = $this->getFirstErrorComponent($form);
        if ($firstErrorComponent) {
            $this->setFormErrorPayload($firstErrorComponent->getName());
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
        $values->userId = $this->presenter->user->id;
        try {
            $this->testGenerator->generateTest($values);
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
    public function handleUpdateFormSuccess(Form $form, ArrayHash $values): void
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
        $values->userId = $this->presenter->user->id;
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
        if (!$this->presenter->getUser()->isInRole('admin')) {
            $problemFilters['filters']['createdBy'] = $this->presenter->user->id;
        }

        // If selected is in JSON string format, decode it
        if ($problemFilters['selected'] && !is_array($problemFilters['selected'])) {
            $problemFilters['selected'] = Json::decode($problemFilters['selected'], Json::FORCE_ARRAY);
        }

        bdump($problemFilters);
        $filterRes = $this->problemRepository->findFiltered($problemFilters['filters']);

        $resFilters[$key] = $problemFilters;

        $this['form']['problem' . $key]->setValue(Json::encode($filterRes));

        $valuesToSetArr = [];
        $valuesToSetObj = [];

        if (isset($problemFilters['selected']) && $problemFilters['selected']) {
            foreach ($problemFilters['selected'] as $selected) {
                if (array_key_exists((int)$selected, $filterRes)) {
                    $valuesToSetArr[] = $selected;
                    $valuesToSetObj[$selected] = $filterRes[$selected];
                    unset($filterRes[$selected]);
                }
            }
        }

        $resFilters[$key]['selected'] = $valuesToSetArr;
        $this['form']['problem' . $key]->setValue(Json::encode($valuesToSetArr));

        $paginator = $this['paginator' . $key]->getPaginator();
        $paginator->itemCount = count($filterRes);
        $paginator->itemsPerPage = 10;

        $filterRes = array_slice($filterRes, $paginator->offset, $paginator->itemsPerPage);

        $this['problemStack' . $key]->setProblems($filterRes, $valuesToSetObj);

        $this->filterSession->setFilters($resFilters);

        $this->presenter->payload->selected = [
            'key' => $key,
            'values' => $valuesToSetArr
        ];

        $this['problemStack' . $key]->redrawControl();
        $this['paginator' . $key]->redrawControl();
    }

    public function setDefaults(): void
    {
        $this['form']->setDefaults([
            'variantsCnt' => $this->entity->getVariantsCnt(),
            'problemsPerVariant' => $this->entity->getProblemsPerVariant(),
            'groups' => $this->entity->getPropertyKeyArray('groups'),
            'term' => $this->entity->getTerm(),
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
        if ($this->isCreate()) {
            $this->template->maxProblems = $this->maxProblems;
            $this->template->problemConditionTypes = $this->problemConditionTypes;
        }
        $this['logoDragAndDrop']->setLogos($this->logos);
        parent::render();
    }
}
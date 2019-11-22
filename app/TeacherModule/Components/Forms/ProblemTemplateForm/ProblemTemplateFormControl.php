<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 17:10
 */

namespace App\TeacherModule\Components\Forms\ProblemTemplateForm;


use App\CoreModule\Arguments\ValidatorArgument;
use App\CoreModule\Components\Forms\EntityFormControl;
use App\TeacherModule\Exceptions\NewtonApiException;
use App\TeacherModule\Exceptions\ProblemTemplateException;
use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Helpers\StringsHelper;
use App\CoreModule\Model\Persistent\Entity\ProblemConditionType;
use App\CoreModule\Model\Persistent\Entity\ProblemType;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use App\CoreModule\Model\Persistent\Repository\SubCategoryRepository;
use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\TeacherModule\Model\NonPersistent\TemplateData\ParametersData;
use App\TeacherModule\Model\NonPersistent\TemplateData\ProblemTemplateStateItem;
use App\TeacherModule\Plugins\ProblemPlugin;
use App\TeacherModule\Services\ProblemTemplateSession;
use App\CoreModule\Services\Validator;
use App\TeacherModule\Services\PluginContainer;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\TextBase;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class ProblemTemplateFormControl
 * @package App\TeacherModule\Components\Forms\ProblemTemplateForm
 */
abstract class ProblemTemplateFormControl extends EntityFormControl
{
    /**
     * @var DifficultyRepository
     */
    protected $difficultyRepository;

    /**
     * @var ProblemTypeRepository
     */
    protected $problemTypeRepository;

    /**
     * @var SubCategoryRepository
     */
    protected $subCategoryRepository;

    /**
     * @var ProblemConditionTypeRepository
     */
    protected $problemConditionTypeRepository;

    /**
     * @var ProblemConditionRepository
     */
    protected $problemConditionRepository;

    /**
     * @var ProblemPlugin
     */
    protected $problemTemplatePlugin;

    /**
     * @var PluginContainer
     */
    protected $pluginContainer;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * @var ProblemType
     */
    protected $problemType;

    /**
     * @var ProblemTemplateSession
     */
    protected $problemTemplateSession;

    /**
     * @var ProblemConditionType[]
     */
    protected $conditionTypes;

    /**
     * @var array
     */
    protected $baseValidation;

    /**
     * @var array
     */
    protected $baseConditionValidation;

    /**
     * @var string
     */
    protected $formName;

    /**
     * ProblemTemplateFormControl constructor.
     * @param Validator $validator
     * @param ConstraintEntityManager $entityManager
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param PluginContainer $pluginContainer
     * @param StringsHelper $stringsHelper
     * @param ConstHelper $constHelper
     * @param ProblemTemplateSession $problemTemplateSession
     */
    public function __construct
    (
        Validator $validator,
        ConstraintEntityManager $entityManager,
        DifficultyRepository $difficultyRepository,
        ProblemTypeRepository $problemTypeRepository,
        SubCategoryRepository $subCategoryRepository,
        ProblemConditionTypeRepository $problemConditionTypeRepository,
        ProblemConditionRepository $problemConditionRepository,
        PluginContainer $pluginContainer,
        StringsHelper $stringsHelper,
        ConstHelper $constHelper,
        ProblemTemplateSession $problemTemplateSession
    )
    {
        parent::__construct($validator, $entityManager);
        $this->difficultyRepository = $difficultyRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->problemConditionTypeRepository = $problemConditionTypeRepository;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->pluginContainer = $pluginContainer;
        $this->stringsHelper = $stringsHelper;
        $this->constHelper = $constHelper;
        $this->problemTemplateSession = $problemTemplateSession;
    }

    /**
     * @param int $problemTypeId
     */
    public function attachEntities(int $problemTypeId): void
    {
        $this->problemType = $this->problemTypeRepository->find($problemTypeId);

        // Get condition types for user interaction by problemType ID
        $this->conditionTypes = $this->problemConditionTypeRepository->findNonValidation($this->problemType->getId());
    }

    public function restoreDefaults(): void
    {
        $formComponents = $this['form']->getComponents();

        foreach ($formComponents as $key => $formComponent) {
            if ($formComponent instanceof TextBase || $formComponent instanceof SelectBox) {
                $formComponent->setValue(null);
            }
        }
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $difficulties = $this->difficultyRepository->findAssoc([], 'id');
        $subcategories = $this->subCategoryRepository->findAssoc([], 'id');

        $form->addHidden('type')
            ->setHtmlId('type');
        $form['type']->setDefaultValue($this->problemType->getId());

        $form->addSelect('subCategory', 'Podkategorie *', $subcategories)
            ->setPrompt('Zvolte podkategorii')
            ->setHtmlAttribute('class', 'form-control');

        $form->addSelect('studentVisible', 'Zobrazit ve cvičebnici *', [
            1 => 'Ano',
            0 => 'Ne'
        ])
            ->setHtmlAttribute('class', 'form-control');

        $form->addTextArea('textBefore', 'Úvod zadání')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Úvodní text zadání.')
            ->setHtmlId('before');

        $form->addTextArea('body', 'Šablona *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Sem patří samotné zadání úlohy.')
            ->setHtmlId('body');

        $form->addTextArea('textAfter', 'Dodatek zadání')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Dodatečný text k zadání.')
            ->setHtmlId('after');

        $form->addSelect('difficulty', 'Obtížnost *', $difficulties)
            ->setPrompt('Zvolte obtížnost')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('difficulty');

        // Attach corresponding ProblemTypeConditions
        foreach ($this->conditionTypes as $conditionType) {
            $form->addSelect('condition_' . $conditionType->getId(), $conditionType->getLabel(), $conditionType->getProblemConditions()->getValues())
                ->setHtmlAttribute('class', 'form-control condition')
                ->setHtmlId('condition-' . $conditionType->getId());
        }

        return $form;
    }

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @param bool $conditions
     * @return bool
     */
    public function validateBaseItems(ProblemTemplateNP $problemTemplate, bool $conditions = false): bool
    {
        bdump('VALIDATE BASE ITEMS');
        $validateFields = [];
        if (!$conditions) {
            foreach ($this->baseValidation as $item) {
                $validateFields[$item['field']] = new ValidatorArgument($problemTemplate->{$item['getter']}(), $item['validation']);
            }
        } else {
            foreach ($this->baseConditionValidation as $item) {
                $validateFields[$item['field']] = new ValidatorArgument($problemTemplate->{$item['getter']}(), $item['validation']);
            }
        }

        try {
            $form = $this->validator->validate($this['form'], $validateFields);
        } catch (\Exception $e) {
            if ($e instanceof NewtonApiException) {
                $this['form']['submit']->addError($e->getMessage());
            } else {
                $this['form']['body']->addError($e->getMessage());
            }
            return false;
        }

        if ($form->hasErrors()) {
            return false;
        }

        return true;
    }

    /**
     * @param array $data
     * @throws \App\CoreModule\Exceptions\ValidatorException
     */
    public function handleCondValidation(array $data): void
    {
        bdump('HANDLE COND VALIDATION');

        $data = ArrayHash::from($data);
        bdump($data);

        $this->redrawControl('flashesSnippet');

        $entityNew = $this->createNonPersistentEntity($data);
        bdump($entityNew);

        // VALIDATE BODY
        if (!$this->validateBody($entityNew)) {
            $this->redrawErrors(false);
            return;
        }

        // If validation was already triggered (after redirect)
        if ($entity = $this->problemTemplateSession->getProblemTemplate()) {

            bdump('VALIDATION WAS ALREADY TRIGGERED');
            bdump($entity);
            bdump($entityNew);

            // Get old ProblemTemplate body
            $bodyOld = $entity->getBody();

            // Actualize entityOld with actual values
            $entity->setValues($data);
            $entity = $this->preprocess($entity);

            // If the body has changes, set all the validation states to false
            if (Strings::trim($bodyOld) !== Strings::trim($entityNew->getBody())) {
                bdump('BODY CHANGED');
                $entity->getState()->invalidate();
            }

            // If the type is not validated, validate it
            if (!$entity->getState()->isTypeValidated()) {
                bdump('VALIDATE TYPE');
                if (!$this->validateType($entity)) {
                    $this->redrawErrors();
                    return;
                }
            }

            // VALIDATE SPECIFIED CONDITION
            if (!$this->validateCondition($this->problemTemplateSession->getProblemTemplate())) {
                $this->redrawErrors(false);
                return;
            }

        } // If it wasn't triggered
        else {

            bdump('FIRST VALIDATION AFTER REDIRECT');

            $entityNew->setParametersData(new ParametersData($this->stringsHelper::extractParametersInfo($entityNew->getBody())));

            // STANDARDIZE THE INPUT
            $entityNew = $this->preprocess($entityNew);
            if ($entityNew === null) {
                $this->redrawErrors();
                return;
            }

            // VALIDATE TYPE
            if (!$this->validateType($entityNew)) {
                $this->redrawErrors();
                return;
            }

            // Pass new ProblemTemplate into session
            $this->problemTemplateSession->setProblemTemplate($entityNew);

            // VALIDATE SPECIFIED CONDITION
            if (!$this->validateCondition($this->problemTemplateSession->getProblemTemplate())) {
                $this->redrawErrors(false);
                return;
            }

        }

        $this->flashMessage('Podmínka je splnitelná.', 'success');
        $this->redrawControl('flashesSnippet');

        // REDRAW ERRORS
        $this->redrawErrors(false);

        bdump('HANDLE COND VALIDATION RES');
        bdump($this->problemTemplateSession->getProblemTemplate());

        $this->presenter->setPayload(true);
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        bdump('HANDLE FORM VALIDATE');
        bdump($this->problemTemplateSession->getProblemTemplate());

        $values = $form->getValues();
        bdump($values);

        $entityNew = $this->createNonPersistentEntity($values);

        // VALIDATE BASE ITEMS
        if (!$this->validateBaseItems($entityNew)) {
            $this->redrawErrors();
            return;
        }

        // VALIDATE BODY
        if (!$this->validateBody($entityNew)) {
            $this->redrawErrors();
            return;
        }

        // If validation was already triggered (after redirect)
        if ($entity = $this->problemTemplateSession->getProblemTemplate()) {

            bdump('VALIDATION WAS ALREADY TRIGGERED');
            bdump($entity);
            bdump($entityNew);

            // Get old ProblemTemplate body
            $bodyOld = $entity->getBody();

            // Actualize entityOld with actual values
            $entity->setValues($values);
            $entity = $this->preprocess($entity);

            // If the body has changes, set all the validation states to false
            if (Strings::trim($bodyOld) !== Strings::trim($entityNew->getBody())) {
                bdump('BODY CHANGED');
                $entity->getState()->invalidate();
            }

            // If the type is not validated, validate it
            if (!$entity->getState()->isTypeValidated()) {
                bdump('VALIDATE TYPE');
                if (!$this->validateType($entity)) {
                    $this->redrawErrors();
                    return;
                }
            }

            // It there are conditions to be validated, force user to validate them
            if ($this->isUpdate()) {
                if ($this->conditionsToValidateUpdate($values)) {
                    bdump('CONDITION NEEDS TO BE VALIDATED: UPDATE');
                    $form['submit']->addError('Ověřte prosím zadanou podmínku.');
                    $this->redrawErrors();
                    return;
                }
            } else {
                if ($this->conditionsToValidateCreate($values)) {
                    bdump('CONDITION NEEDS TO BE VALIDATED: CREATE');
                    $form['submit']->addError('Ověřte prosím zadanou podmínku.');
                    $this->redrawErrors();
                    return;
                }
            }

        } // If it wasn't triggered
        else {

            bdump('FIRST VALIDATION AFTER REDIRECT');

            $entityNew->setParametersData(new ParametersData($this->stringsHelper::extractParametersInfo($entityNew->getBody())));

            // STANDARDIZE THE INPUT
            $entityNew = $this->preprocess($entityNew);
            if ($entityNew === null) {
                $this->redrawErrors();
                return;
            }

            // VALIDATE TYPE
            if (!$this->validateType($entityNew)) {
                $this->redrawErrors();
                return;
            }

            // Pass new ProblemTemplate into session
            $this->problemTemplateSession->setProblemTemplate($entityNew);

            bdump($entityNew);

            if ($this->conditionsToValidateCreate($values)) {
                $form['submit']->addError('Ověřte prosím zadanou podmínku.');
                $this->redrawErrors();
                return;
            }

        }

        // REDRAW ERRORS
        $this->redrawErrors();
    }

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return bool
     */
    public function validateBody(ProblemTemplateNP $problemTemplate): bool
    {
        $validateFields['body'] = new ValidatorArgument($problemTemplate, 'body_' . $problemTemplate->getType());

        try {
            $form = $this->validator->validate($this['form'], $validateFields);
        } catch (\Exception $e) {
            $this['form']['body']->addError($e->getMessage());
            $this->redrawErrors();
            return false;
        }

        if ($form->hasErrors()) {
            $this->redrawErrors();
            return false;
        }

        return true;
    }

    /**
     * @param ProblemTemplateNP $problemTemplateNP
     * @return bool
     */
    public function validateType(ProblemTemplateNP $problemTemplateNP): bool
    {
        bdump('VALIDATE TYPE');

        try {
            if ($this->pluginContainer->getPlugin($this->problemType->getKeyLabel())->validateType($problemTemplateNP)) {
                $problemTemplateNP->getState()->update(new ProblemTemplateStateItem('type', true, true));
            } else {
                $problemTemplateNP->getState()->update(new ProblemTemplateStateItem('type', false, true));
                $this->redrawErrors(false);
                return false;
            }
        } catch (\Exception $e) {
            bdump($e);
            $problemTemplateNP->getState()->update(new ProblemTemplateStateItem('type', false, true));
            $this['form']['body']->addError($e->getMessage());
            $this->redrawErrors(false);
            return false;
        }

        return true;
    }

    /**
     * @param ProblemTemplateNP $data
     * @return bool
     * @throws \App\CoreModule\Exceptions\ValidatorException
     */
    public function validateCondition(ProblemTemplateNP $data): bool
    {
        $validationFields['condition_' . $data->getConditionType()] = new ValidatorArgument(
            $data, 'condition_' . $data->getConditionType(), 'condition_' . $data->getConditionType()
        );

        // Validate template condition
        try {
            $form = $this->validator->validate($this['form'], $validationFields);
            if ($form->hasErrors()) {
                $data->getState()->update(new ProblemTemplateStateItem('condition_' . $data->getConditionType(), $data->getConditionAccessor(), false));
                return false;
            }
        } catch (ProblemTemplateException $e) {
            $this['form']['body']->addError($e->getMessage());
            $data->getState()->update(new ProblemTemplateStateItem('condition_' . $data->getConditionType(), $data->getConditionAccessor(), false));
            return false;
        }

        $data->getState()->update(new ProblemTemplateStateItem('condition_' . $data->getConditionType(), $data->getConditionAccessor(), true));

        return true;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleFormSuccess(Form $form, ArrayHash $values): void
    {
        bdump('HANDLE FORM SUCCESS');
        try {
            $values->userId = $this->presenter->user->id;
            $this->functionality->create($values);
            $this->onSuccess();
        } catch (\Exception $e) {
            bdump($e);
            if ($e instanceof AbortException) {
                return;
            }
            $this->onError($e);
        }
        $this->problemTemplateSession->erase();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleUpdateFormSuccess(Form $form, ArrayHash $values): void
    {
        bdump('HANDLE FORM SUCCESS');
        try {
            $this->functionality->update($this->entity->getId(), $values);
            $this->onSuccess();
        } catch (\Exception $e) {
            bdump($e);
            if ($e instanceof AbortException) {
                return;
            }
            $this->onError($e);
        }
    }

    /**
     * @param ArrayHash $values
     * @return bool
     */
    public function conditionsToValidateCreate(ArrayHash $values): bool
    {
        bdump('CONDITIONS TO VALIDATE CREATE');
        // If the validation was already triggered
        if ($this->problemTemplateSession->getProblemTemplate()) {
            return !$this->problemTemplateSession->getProblemTemplate()->getState()->conditionsValidated($values);
        }
        // If the validation wasn't triggered yet
        foreach ($values as $key => $value) {
            if ($value !== 0 && Strings::match($key, '~condition_\d~')) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param ArrayHash $values
     * @return bool
     */
    public function conditionsToValidateUpdate(ArrayHash $values): bool
    {
        bdump('CONDITIONS TO VALIDATE UPDATE');
        $conditions = $this->entity->getConditions()->getValues();
        bdump($conditions);
        if ($this->problemTemplateSession->getProblemTemplate()) {
            return !$this->problemTemplateSession->getProblemTemplate()->getState()->conditionsValidated($values);
        } else {
            foreach ($conditions as $condition) {
                $conditionTypeId = $condition->getProblemConditionType()->getId();
                bdump([$values->{'condition_' . $conditionTypeId}, $condition->getAccessor()]);
                if ($values->{'condition_' . $conditionTypeId} !== 0 && $values->{'condition_' . $conditionTypeId} !== $condition->getAccessor()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @throws \Exception
     */
    public function render(): void
    {
        bdump('RENDER');
        $this->template->problemType = $this->problemType;
        $this->template->conditionTypes = $this->conditionTypes;
        parent::render();
    }

    public function setDefaults(): void
    {
        if (!$this->entity) {
            return;
        }

        $this['form']['id']->setDefaultValue($this->entity->getId());
        $this['form']['subCategory']->setDefaultValue($this->entity->getSubCategory()->getId());
        $this['form']['textBefore']->setDefaultValue($this->entity->getTextBefore());
        $this['form']['body']->setDefaultValue($this->entity->getBody());
        $this['form']['textAfter']->setDefaultValue($this->entity->getTextAfter());
        $this['form']['difficulty']->setDefaultValue($this->entity->getDifficulty()->getId());
        $this['form']['studentVisible']->setDefaultValue((int) $this->entity->isStudentVisible());

        $conditions = $this->entity->getConditions()->getValues();

        foreach ($conditions as $condition) {
            $this['form']['condition_' . $condition->getProblemConditionType()->getId()]->setValue($condition->getAccessor());
        }
    }

    /**
     * @param bool $submitted
     */
    public function redrawErrors(bool $submitted = true): void
    {
        parent::redrawErrors($submitted);
        $this->redrawControl('conditionsErrorSnippet');
        $this->redrawControl('flashesSnippet');
        $this->redrawControl('submitErrorSnippet');
    }

    /**
     * @param ArrayHash $values
     * @return mixed
     */
    abstract protected function createNonPersistentEntity(ArrayHash $values): ProblemTemplateNP;

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return ProblemTemplateNP|null
     */
    abstract public function preprocess(ProblemTemplateNP $problemTemplate): ?ProblemTemplateNP;
}
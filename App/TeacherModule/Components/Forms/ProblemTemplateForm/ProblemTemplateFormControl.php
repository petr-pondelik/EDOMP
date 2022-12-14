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
use App\TeacherModule\Exceptions\InvalidParameterException;
use App\TeacherModule\Exceptions\NewtonApiException;
use App\TeacherModule\Exceptions\ProblemTemplateException;
use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Model\Persistent\Entity\ProblemConditionType;
use App\CoreModule\Model\Persistent\Entity\ProblemType;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;
use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\TeacherModule\Model\NonPersistent\TemplateData\ParametersData;
use App\TeacherModule\Model\NonPersistent\TemplateData\ProblemTemplateStateItem;
use App\TeacherModule\Plugins\ProblemPlugin;
use App\TeacherModule\Services\ParameterParser;
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
     * @var ProblemPlugin
     */
    protected $problemTemplatePlugin;

    /**
     * @var PluginContainer
     */
    protected $pluginContainer;

    /**
     * @var ParameterParser
     */
    protected $parameterParser;

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
     * @param SubThemeRepository $subThemeRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param PluginContainer $pluginContainer
     * @param ParameterParser $parameterParser
     * @param ConstHelper $constHelper
     * @param ProblemTemplateSession $problemTemplateSession
     */
    public function __construct
    (
        Validator $validator,
        ConstraintEntityManager $entityManager,
        DifficultyRepository $difficultyRepository,
        ProblemTypeRepository $problemTypeRepository,
        SubThemeRepository $subThemeRepository,
        ProblemConditionTypeRepository $problemConditionTypeRepository,
        ProblemConditionRepository $problemConditionRepository,
        PluginContainer $pluginContainer,
        ParameterParser $parameterParser,
        ConstHelper $constHelper,
        ProblemTemplateSession $problemTemplateSession
    )
    {
        parent::__construct($validator, $entityManager);
        $this->difficultyRepository = $difficultyRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->subThemeRepository = $subThemeRepository;
        $this->problemConditionTypeRepository = $problemConditionTypeRepository;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->pluginContainer = $pluginContainer;
        $this->parameterParser = $parameterParser;
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
        $subThemes = $this->subThemeRepository->findAllowed($this->presenter->user);

        $form->addHidden('type')
            ->setHtmlId('type');
        $form['type']->setDefaultValue($this->problemType->getId());

        $form->addSelect('subTheme', 'Podt??ma *', $subThemes)
            ->setPrompt('Zvolte podt??ma')
            ->setHtmlAttribute('class', 'form-control');

        $form->addSelect('studentVisible', 'Zobrazit ve cvi??ebnici *', [
            1 => 'Ano',
            0 => 'Ne'
        ])
            ->setHtmlAttribute('class', 'form-control');

        $form->addTextArea('textBefore', '??vod zad??n??')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', '??vodn?? text zad??n??')
            ->setHtmlId('before');

        $form->addTextArea('body', '??ablona *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Sem pat???? samotn?? zad??n?? ??lohy')
            ->setHtmlId('body');

        $form->addTextArea('textAfter', 'Dodatek zad??n??')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Dodate??n?? text k zad??n??')
            ->setHtmlId('after');

        $form->addSelect('difficulty', 'Obt????nost *', $difficulties)
            ->setPrompt('Zvolte obt????nost')
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

        $this->redrawControl('flashesSnippet');

        $entityNew = $this->createNonPersistentEntity($data);

        // VALIDATE BODY
        if (!$this->validateBody($entityNew)) {
            $this->redrawErrors(false);
            return;
        }

        // If validation was already triggered (after redirect)
        if ($entity = $this->problemTemplateSession->getProblemTemplate()) {

            bdump('VALIDATION WAS ALREADY TRIGGERED');

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

            $entityNew->setParametersData(new ParametersData($this->parameterParser::extractParametersInfo($entityNew->getBody())));

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

        $this->flashMessage('Podm??nka je splniteln??.', 'conditionSuccess');
        $this->redrawControl('flashesSnippet');

        // REDRAW ERRORS
        $this->redrawErrors(false);

        $this->presenter->setPayload('response', true);
    }

    /**
     * @param Form $form
     * @throws \App\TeacherModule\Exceptions\InvalidParameterException
     */
    public function handleFormValidate(Form $form): void
    {
        bdump('HANDLE FORM VALIDATE');

        $values = $form->getValues();

        $entityNew = $this->createNonPersistentEntity($values);

        try {
            $entityNew->setParametersData(new ParametersData($this->parameterParser::extractParametersInfo($entityNew->getBody())));
        } catch (InvalidParameterException $e) {
            $this['form']['body']->addError($e->getMessage());
            $this->redrawErrors();
            return;
        }

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

            // Get old ProblemTemplate body
            $bodyOld = $entity->getBody();

            // Actualize entityOld with actual values
            $entity->setValues($values);
            $entity->setParametersData($entityNew->getParametersData());
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
                if ($toValidateItem = $this->conditionsToValidateUpdate($values)) {
                    bdump('CONDITION NEEDS TO BE VALIDATED: UPDATE');
                    if (!$toValidateItem['validated']) {
                        $form['condition_' . $toValidateItem['toValidate'][0]]->addError('Ov????te pros??m zadanou podm??nku.');
                    }
                    $this->redrawErrors();

                    $firstErrorComponent = $this->getFirstErrorComponent($form);
                    if ($firstErrorComponent) {
                        $this->setFormErrorPayload($firstErrorComponent->getName());
                    }

                    return;
                }
            } else if ($this->conditionsToValidateCreate($values)) {
                bdump('CONDITION NEEDS TO BE VALIDATED: CREATE');
                $form['submit']->addError('Ov????te pros??m zadanou podm??nku.');
                $this->redrawErrors();
                return;
            }

        } // If it wasn't triggered
        else {

            bdump('FIRST VALIDATION AFTER REDIRECT');
            bdump($this->problemTemplateSession->getProblemTemplate());

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

            // It there are conditions to be validated, force user to validate them
            if ($this->isUpdate()) {
                if ($toValidateItem = $this->conditionsToValidateUpdate($values)) {
                    bdump('CONDITION NEEDS TO BE VALIDATED: UPDATE');
                    if (!$toValidateItem['validated']) {
                        $form['condition_' . $toValidateItem['toValidate'][0]]->addError('Ov????te pros??m zadanou podm??nku.');
                    }
                    $this->redrawErrors();

                    $firstErrorComponent = $this->getFirstErrorComponent($form);
                    if ($firstErrorComponent) {
                        $this->setFormErrorPayload($firstErrorComponent->getName());
                    }

                    return;
                }
            } else if ($this->conditionsToValidateCreate($values)) {
                bdump('CONDITION NEEDS TO BE VALIDATED: CREATE');
                $form['submit']->addError('Ov????te pros??m zadanou podm??nku.');
                $this->redrawErrors();
                return;
            }

            // Pass new ProblemTemplate into session
            $this->problemTemplateSession->setProblemTemplate($entityNew);
        }

        // REDRAW ERRORS
        $this->redrawErrors();

        $firstErrorComponent = $this->getFirstErrorComponent($form);
        if ($firstErrorComponent) {
            $this->setFormErrorPayload($firstErrorComponent->getName());
        }
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
                $problemTemplateNP->getState()->update(new ProblemTemplateStateItem('type', false, false));
                $this->redrawErrors(false);
                return false;
            }
        } catch (\Exception $e) {
            bdump($e);
            $problemTemplateNP->getState()->update(new ProblemTemplateStateItem('type', false, false));
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
        bdump($values);
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
    public function conditionsToValidateUpdate(ArrayHash $values): array
    {
        bdump('CONDITIONS TO VALIDATE UPDATE');
        $conditions = $this->entity->getConditions()->getValues();
        if ($this->problemTemplateSession->getProblemTemplate()) {
            $validatedRes = $this->problemTemplateSession->getProblemTemplate()->getState()->conditionsValidated($values);
            return $validatedRes;
        } else {
            foreach ($conditions as $condition) {
                $conditionTypeId = $condition->getProblemConditionType()->getId();
                if ($values->{'condition_' . $conditionTypeId} !== 0 && $values->{'condition_' . $conditionTypeId} !== $condition->getAccessor()) {
                    return [
                        'validated' => false,
                        'toValidate' => [$conditionTypeId]
                    ];
                }
            }
        }
        return [
            'validated' => true,
            'toValidate' => []
        ];
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
        $this['form']['subTheme']->setDefaultValue($this->entity->getSubTheme()->getId());
        $this['form']['textBefore']->setDefaultValue($this->entity->getTextBefore());
        $this['form']['body']->setDefaultValue($this->entity->getBody());
        $this['form']['textAfter']->setDefaultValue($this->entity->getTextAfter());
        $this['form']['difficulty']->setDefaultValue($this->entity->getDifficulty()->getId());
        $this['form']['studentVisible']->setDefaultValue((int)$this->entity->isStudentVisible());

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
        $this->redrawControl('submitErrorSnippet');
        $this->redrawControl('flashesSnippet');
        $this->redrawControl('conditionsFlashesSnippet');
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
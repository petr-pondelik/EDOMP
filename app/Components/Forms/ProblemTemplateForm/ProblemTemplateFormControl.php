<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 17:10
 */

namespace App\Components\Forms\ProblemTemplateForm;


use App\Arguments\ValidatorArgument;
use App\Components\Forms\EntityFormControl;
use App\Exceptions\NewtonApiException;
use App\Exceptions\ProblemTemplateException;
use App\Helpers\ConstHelper;
use App\Helpers\StringsHelper;
use App\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\Model\NonPersistent\TemplateData\ProblemTemplateStateItem;
use App\Model\NonPersistent\TemplateData\ParametersData;
use App\Model\Persistent\Entity\ProblemConditionType;
use App\Model\Persistent\Entity\ProblemType;
use App\Model\Persistent\Functionality\BaseFunctionality;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\ProblemConditionRepository;
use App\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Plugins\ProblemPlugin;
use App\Services\PluginContainer;
use App\Services\ProblemTemplateSession;
use App\Services\Validator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\TextBase;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class ProblemTemplateFormControl
 * @package App\Components\Forms\ProblemTemplateForm
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
     * @param BaseFunctionality $functionality
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param ProblemPlugin $problemPlugin
     * @param PluginContainer $pluginContainer
     * @param StringsHelper $stringsHelper
     * @param ConstHelper $constHelper
     * @param ProblemTemplateSession $problemTemplateSession
     * @param bool $edit
     */
    public function __construct
    (
        Validator $validator,
        BaseFunctionality $functionality,
        DifficultyRepository $difficultyRepository, ProblemTypeRepository $problemTypeRepository,
        SubCategoryRepository $subCategoryRepository,
        ProblemConditionTypeRepository $problemConditionTypeRepository, ProblemConditionRepository $problemConditionRepository,
        ProblemPlugin $problemPlugin,
        PluginContainer $pluginContainer,
        StringsHelper $stringsHelper, ConstHelper $constHelper,
        ProblemTemplateSession $problemTemplateSession,
        bool $edit = false
    )
    {
        parent::__construct($validator, $edit);
        $this->functionality = $functionality;
        $this->difficultyRepository = $difficultyRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->problemConditionTypeRepository = $problemConditionTypeRepository;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->problemTemplatePlugin = $problemPlugin;
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

        foreach ($formComponents as $key => $formComponent){
            if($formComponent instanceof TextBase || $formComponent instanceof SelectBox){
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

        $form->addHidden('type');
        $form['type']->setDefaultValue($this->problemType->getId());

        $form->addSelect('subCategory', 'Podkategorie *', $subcategories)
            ->setPrompt('Zvolte podkategorii')
            ->setHtmlAttribute('class', 'form-control');

        $form->addTextArea('textBefore', 'Úvod zadání')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Úvodní text zadání.')
            ->setHtmlId('before');

        $form->addTextArea('body', 'Šablona *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder','Sem patří samotné zadání úlohy.')
            ->setHtmlId('body');

        $form->addTextArea('textAfter', 'Dodatek zadání')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Dodatečný text k zadání.')
            ->setHtmlId('after');

        $form->addSelect('difficulty', 'Obtížnost *', $difficulties)
            ->setPrompt('Zvolte obtížnost')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('difficulty');

        //Field for storing all conditions final valid state
        $form->addHidden('conditions_valid')
            ->setDefaultValue(1)
            ->setHtmlId('conditions_valid');

        // Attach corresponding ProblemTypeConditions
        foreach ($this->conditionTypes as $conditionType){
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
        if(!$conditions){
            foreach ($this->baseValidation as $item){
                //bdump($item['field']);
                $validateFields[$item['field']] = new ValidatorArgument($problemTemplate->{$item['getter']}(), $item['validation']);
            }
        }
        else{
            foreach ($this->baseConditionValidation as $item){
                $validateFields[$item['field']] = new ValidatorArgument($problemTemplate->{$item['getter']}(), $item['validation']);
            }
        }

        try{
            $form = $this->validator->validate($this['form'], $validateFields);
        } catch (\Exception $e){
            if($e instanceof NewtonApiException){
                $this['form']['submit']->addError($e->getMessage());
            }
            else{
                $this['form']['body']->addError($e->getMessage());
            }
            return false;
        }

        if($form->hasErrors()){
            return false;
        }

        return true;
    }

    /**
     * @param array $data
     */
    public function handleCondValidation(array $data): void
    {
        bdump('HANDLE COND VALIDATION');
        bdump($data);

        $this->redrawControl('flashesSnippet');

        $this->problemTemplateSession->setProblemTemplate($this->createNonPersistentEntity(ArrayHash::from($data)));

//        $defaultState = $this->problemTemplateSession->getDefaultState()->getProblemTemplateStateItems();
        $this->problemTemplateSession->getProblemTemplate()->getState()->reset();

        bdump($this->problemTemplateSession->getProblemTemplate());

        // VALIDATE BODY
        if(!$this->validateBody($this->problemTemplateSession->getProblemTemplate())){
            $this->redrawErrors(false);
            return;
        }

        $problemData = new ParametersData($this->stringsHelper::extractParametersInfo($this->problemTemplateSession->getProblemTemplate()->getBody()));
        $this->problemTemplateSession->getProblemTemplate()->setParametersData($problemData);

        // STANDARDIZE THE INPUT
        $standardized = $this->standardize($this->problemTemplateSession->getProblemTemplate());
        if($standardized === null){
            $this->redrawErrors(false);
            return;
        }
        $this->problemTemplateSession->setProblemTemplate($standardized);

        bdump($this->problemTemplateSession->getProblemTemplate());

        // VALIDATE TYPE
        if(!$this->validateType($this->problemTemplateSession->getProblemTemplate())){
            $this->redrawErrors(false);
            return;
        }

        bdump($this->problemTemplateSession->getProblemTemplate());

        // VALIDATE SPECIFIED CONDITION
        if(!$this->validateCondition($this->problemTemplateSession->getProblemTemplate())){
            $this->redrawErrors(false);
            return;
        }

        bdump($this->problemTemplateSession->getProblemTemplate());

        $this->flashMessage('Podmínka je splnitelná.', 'success');
        $this->redrawControl('flashesSnippet');

        // REDRAW ERRORS
        $this->redrawErrors(false);

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

        // If condition validation was triggered
        if($this->problemTemplateSession->getProblemTemplate()){

            bdump('CONDITION VALIDATION WAS TRIGGERED');

            // Get old problem template body
            $bodyOld = $this->problemTemplateSession->getProblemTemplate()->getBody();

            // Actualize problemTemplateSession with actual values
            $this->problemTemplateSession->getProblemTemplate()->setValues($values);
            $new = $this->standardize($this->problemTemplateSession->getProblemTemplate());
            $this->problemTemplateSession->setProblemTemplate($new);

            // If template body was changed, the type must be validated again
            // If template's old body was not successfully validated by type, the type must be validated again
            if(
                Strings::trim($bodyOld) !== Strings::trim($values->body) ||
                !$this->problemTemplateSession->getProblemTemplate()->getState()->isTypeValidated()
            ){
                // VALIDATE TYPE
                if(!$this->validateType($this->problemTemplateSession->getProblemTemplate())){
                    $this->redrawErrors();
                    return;
                }
            }

            if(
                Strings::trim($bodyOld) !== Strings::trim($values->body) ||
                $this->conditionsToValidate($values)
//                !$this->problemTemplateSession->getProblemTemplate()->getState()->isAllValidated($this->conditionTypes, $values)
            ){
                bdump('CONDITION NEEDS TO BE VALIDATED');
//                $this->problemTemplateSession->getProblemTemplate()->getState()->invalidateConditions($values);
                $form['submit']->addError('Ověřte prosím zadanou podmínku.');
                $this->redrawErrors();
                return;
            }

            $entity = $this->problemTemplateSession->getProblemTemplate();

        }
        // If it wasn't
        else{

            bdump('FIRST VALIDATION');

            $entity = $this->createNonPersistentEntity($values);

            if($this->edit){
                $defaultState = $this->problemTemplateSession->getDefaultState()->getProblemTemplateStateItems();
                $entity->getState()->updateArr($defaultState);
            }

            bdump($entity);

            // VALIDATE BODY
            if(!$this->validateBody($entity)){
                $this->redrawErrors();
                return;
            }

            $entity->setParametersData(new ParametersData($this->stringsHelper::extractParametersInfo($entity->getBody())));

            // STANDARDIZE THE INPUT
            $entity = $this->standardize($entity);
            if($entity === null){
                $this->redrawErrors();
                return;
            }

            bdump('TEST');

            // VALIDATE TYPE
            if(!$this->validateType($entity)){
                $this->redrawErrors();
                return;
            }

            if($this->conditionsToValidate($values)){
                $form['submit']->addError('Ověřte prosím zadanou podmínku.');
                $this->redrawErrors();
                return;
            }

        }

        bdump($entity);

        // VALIDATE BASE ITEMS
        if(!$this->validateBaseItems($entity)){
            $this->redrawErrors();
            return;
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
        bdump('VALIDATE BODY');
        bdump($problemTemplate);
        $validateFields['body'] = new ValidatorArgument($problemTemplate, 'body_' . $problemTemplate->getType());
        //bdump($validateFields);

        try{
            $form = $this->validator->validate($this['form'], $validateFields);
        } catch (\Exception $e){
            $this['form']['body']->addError($e->getMessage());
            $this->redrawErrors();
            return false;
        }

        if($form->hasErrors()){
            $this->redrawErrors();
            return false;
        }

        return true;
    }

    /**
     * @param ProblemTemplateNP $data
     * @return bool
     */
    public function validateType(ProblemTemplateNP $data): bool
    {
        bdump('VALIDATE TYPE');

        try{
            if($this->problemTemplatePlugin->validateType($data)){
                $data->getState()->update(new ProblemTemplateStateItem('type', true, true));
            }
            else{
                $data->getState()->update(new ProblemTemplateStateItem('type', false, true));
                $this['form']['body']->addError('TEST');
                $this->redrawErrors(false);
                return false;
            }
        } catch (\Exception $e){
            bdump($e);
            $data->getState()->update(new ProblemTemplateStateItem('type', false, true));
            $this['form']['body']->addError($e->getMessage());
            $this->redrawErrors(false);
            return false;
        }

        return true;
    }

    /**
     * @param ProblemTemplateNP $data
     * @return bool
     */
    public function validateCondition(ProblemTemplateNP $data): bool
    {
        $validationFields['condition_' . $data->getConditionType()] = new ValidatorArgument(
            $data, 'condition_' . $data->getConditionType(), 'condition_' . $data->getConditionType()
        );

        // Validate template condition
        try{
            $form = $this->validator->validate($this['form'], $validationFields);
            if($form->hasErrors()){
                $data->getState()->update(new ProblemTemplateStateItem('condition_' . $data->getConditionType(), $data->getConditionAccessor(), false));
                return false;
            }
        } catch (ProblemTemplateException $e){
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
        try{
            $this->functionality->create($values);
            $this->onSuccess();
        } catch (\Exception $e){
            bdump($e);
            if ($e instanceof AbortException){
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
    public function handleEditFormSuccess(Form $form, ArrayHash $values): void
    {
        bdump('HANDLE FORM SUCCESS');
        bdump($values);
        try{
            $this->functionality->update($values->idHidden, $values);
            $this->onSuccess();
        } catch (\Exception $e){
            if ($e instanceof AbortException){
                return;
            }
            $this->onError($e);
        }
    }

    /**
     * @param ArrayHash $values
     * @return bool
     */
    public function conditionsToValidate(ArrayHash $values): bool
    {
        bdump('CONDITIONS TO VALIDATE');

        // In the case of edit
        if($this->edit){
            $conditions = $this->entity->getConditions()->getValues();
            bdump($conditions);
            if($this->problemTemplateSession->getProblemTemplate()){
                return !$this->problemTemplateSession->getProblemTemplate()->getState()->conditionsValidated($values);
            }
            else{
                foreach ($conditions as $condition){
                    $conditionTypeId = $condition->getProblemConditionType()->getId();
                    bdump([$values->{'condition_' . $conditionTypeId}, $condition->getAccessor()]);
                    if($values->{'condition_' . $conditionTypeId} !== 0 && $values->{'condition_' . $conditionTypeId} !== $condition->getAccessor()){
                        return true;
                    }
                }
            }

        }
        // In the case of create
        else{
            // If the validation was already triggered
            if($this->problemTemplateSession->getProblemTemplate()) {
                return !$this->problemTemplateSession->getProblemTemplate()->getState()->conditionsValidated($values);
            }
            // If the validation wasn't triggered yet
            foreach ($values as $key => $value){
                if($value !== 0 && Strings::match($key, '~condition_\d~')){
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
        bdump($this->entity);
        $this->template->entity = $this->entity;
        $this->template->problemType = $this->problemType;
        $this->template->conditionTypes = $this->conditionTypes;
        if($this->edit){
            $this->template->render(__DIR__ . '/' . $this->formName . '/templates/edit.latte');
        }
        else{
            $this->template->render(__DIR__ . '/' . $this->formName . '/templates/create.latte');
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
    abstract public function standardize(ProblemTemplateNP $problemTemplate): ?ProblemTemplateNP;
}
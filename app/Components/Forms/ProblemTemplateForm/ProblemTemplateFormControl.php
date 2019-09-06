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
use App\Model\NonPersistent\Entity\ProblemTemplateStatusItem;
use App\Model\NonPersistent\Parameter\ParametersData;
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
use App\Services\ProblemTemplateStatus;
use App\Services\Validator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\TextBase;
use Nette\Utils\ArrayHash;

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
     * @var ProblemTemplateStatus
     */
    protected $problemTemplateStatus;

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
     * @param ProblemTemplateStatus $problemTemplateStatus
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
        ProblemTemplateStatus $problemTemplateStatus,
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
        $this->problemTemplateStatus = $problemTemplateStatus;
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
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        bdump('HANDLE FORM VALIDATE');
        $data = $this->createNonPersistentEntity($form->getValues());

        // VALIDATE BASE ITEMS
        if(!$this->validateBaseItems($data)){
            $this->redrawErrors();
            return;
        }

        // VALIDATE BODY
        if(!$this->validateBody($data)){
            $this->redrawErrors();
            return;
        }

        $data->setParametersData(new ParametersData($this->stringsHelper::extractParametersInfo($data->getBody())));

        // STANDARDIZE THE INPUT
        $data = $this->standardize($data);
        if($data === null){
            $this->redrawErrors();
            return;
        }

        // VALIDATE TYPE
        if(!$this->validateType($data)){
            $this->redrawErrors();
            return;
        }

        // VALIDATE IF ALL CONDITIONS ARE SATISFIED
        $validateFields['conditions_valid'] = new ValidatorArgument($form->getValues()->conditions_valid, 'isTrue', 'submit');
        $this->validator->validate($form, $validateFields);

        // REDRAW ERRORS
        $this->redrawErrors();
    }

    /**
     * @param array $data
     */
    public function handleTypeValidation(array $data): void
    {
        bdump('HANDLE TYPE VALIDATION');
        $this->redrawControl('flashesSnippet');

        $data['type'] = $this->problemType->getId();
        $data['idHidden'] = $data['idHidden'] ?: null;

        $entity = $this->createNonPersistentEntity(ArrayHash::from($data));

        // VALIDATE BODY
        if(!$this->validateBody($entity)){
            $this->redrawErrors(false);
            return;
        }

        $entity->setParametersData(new ParametersData($this->stringsHelper::extractParametersInfo($entity->getBody())));

        // STANDARDIZE THE INPUT
        $standardized = $this->standardize($entity);
        if($standardized === null){
            $this->redrawErrors(false);
            return;
        }

        // VALIDATE TYPE
        if(!$this->validateType($standardized)){
            $this->redrawErrors(false);
            return;
        }

        $this->flashMessage('Ze zadané šablony lze vygenerovat úlohy typu ' . $this->problemType->getLabel() . '.', 'success');
        $this->redrawControl('flashesSnippet');

        $this->redrawErrors(false);

        $this->presenter->setPayload(true);
    }

    /**
     * @param array $data
     */
    public function handleCondValidation(array $data): void
    {
        bdump('HANDLE COND VALIDATION');
        $this->redrawControl('flashesSnippet');

        $entity = $this->createNonPersistentEntity(ArrayHash::from($data));

        if(!$this->problemTemplateStatus->isTypeValidated()){
            $this['form']['condition_' . $entity->getConditionType()]->addError('Nejdříve ověřte šablonu.');
            $this->redrawErrors(false);
            return;
        }

        // VALIDATE BASE ITEMS
        if(!$this->validateBaseItems($entity, true)){
            $this->redrawErrors(false);
            return;
        }

        $entity->setParametersData(new ParametersData($this->stringsHelper::extractParametersInfo($entity->getBody())));

        // STANDARDIZE THE INPUT
        $standardized = $this->standardize($entity);
        if($standardized === null){
            $this->redrawErrors(false);
            return;
        }

        // VALIDATE SPECIFIED CONDITION
        if(!$this->validateCondition($standardized)){
            $this->redrawErrors(false);
            return;
        }

        $this->flashMessage('Podmínka je splnitelná.', 'success');
        $this->redrawControl('flashesSnippet');

        // REDRAW ERRORS
        $this->redrawErrors(false);

        $this->presenter->setPayload(true);
    }

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return bool
     */
    public function validateBody(ProblemTemplateNP $problemTemplate): bool
    {
        //bdump('VALIDATE BODY');
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
        bdump($data);

        try{
            if($this->problemTemplatePlugin->validateType($data)){
                $this->problemTemplateStatus->updateStatus(new ProblemTemplateStatusItem('type', true));
            }
            else{
                $this->problemTemplateStatus->updateStatus(new ProblemTemplateStatusItem('type', false));
            }
        } catch (\Exception $e){
            $this->problemTemplateStatus->updateStatus(new ProblemTemplateStatusItem('type', false));
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
                $this->problemTemplateStatus->updateStatus(new ProblemTemplateStatusItem($data->getConditionType(), false));
                return false;
            }
        } catch (ProblemTemplateException $e){
            $this['form']['body']->addError($e->getMessage());
            $this->problemTemplateStatus->updateStatus(new ProblemTemplateStatusItem($data->getConditionType(), false));
            return false;
        }

        $this->problemTemplateStatus->updateStatus(new ProblemTemplateStatusItem($data->getConditionType(), true));

        bdump($this->problemTemplateStatus->getSerialized());

        return true;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleFormSuccess(Form $form, ArrayHash $values): void
    {
        try{
            $this->functionality->create($values);
            $this->onSuccess();
        } catch (\Exception $e){
            if ($e instanceof AbortException){
                return;
            }
            $this->onError($e);
        }
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleEditFormSuccess(Form $form, ArrayHash $values): void
    {
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
     * @throws \Exception
     */
    public function render(): void
    {
        //bdump('RENDER');
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
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
use App\Model\Entity\ProblemConditionType;
use App\Model\Entity\ProblemType;
use App\Model\Functionality\BaseFunctionality;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemConditionTypeRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Services\MathService;
use App\Services\Validator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
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
     * @var MathService
     */
    protected $mathService;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * @var int
     */
    protected $problemTypeId;

    /**
     * @var ProblemType
     */
    protected $problemType;

    /**
     * @var ProblemConditionType[]
     */
    protected $conditionTypes;

    /**
     * @var array
     */
    protected $baseItems;

    /**
     * @var array
     */
    protected $baseItemsCondition;

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
     * @param MathService $mathService
     * @param ConstHelper $constHelper
     * @param bool $edit
     */
    public function __construct
    (
        Validator $validator,
        BaseFunctionality $functionality,
        DifficultyRepository $difficultyRepository, ProblemTypeRepository $problemTypeRepository,
        SubCategoryRepository $subCategoryRepository,
        ProblemConditionTypeRepository $problemConditionTypeRepository, ProblemConditionRepository $problemConditionRepository,
        MathService $mathService, ConstHelper $constHelper,
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
        $this->mathService = $mathService;
        $this->constHelper = $constHelper;
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

        $form->addTextArea('body', 'Úloha *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder','Sem patří samotné zadání úlohy.')
            ->setHtmlId('body');

        $form->addText('variable', 'Neznámá *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Neznámá šablony.')
            ->setHtmlId('variable');

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
     * @param ArrayHash $values
     * @param bool $conditions
     * @return bool
     */
    public function validateBaseItems(ArrayHash $values, bool $conditions = false): bool
    {
        $validateFields = [];
        if(!$conditions){
            foreach ($this->baseItems as $item){
                $validateFields[$item['field']] = new ValidatorArgument($values[$item['field']], $item['validation']);
            }
        }
        else{
            foreach ($this->baseItemsCondition as $item){
                $validateFields[$item['field']] = new ValidatorArgument($values[$item['field']], $item['validation']);
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

        $values = $form->getValues();

        // VALIDATE BASE ITEMS
        if(!$this->validateBaseItems($values)){
            bdump('RETURN');
            $this->redrawErrors();
            return;
        }

        // VALIDATE BODY
        if(!$this->validateBody($values)){
            bdump('VALIDATE BODY');
            $this->redrawErrors();
            return;
        }

        // STANDARDIZE THE INPUT
        $standardized = $this->standardize($values);
        if($standardized === null){
            $this->redrawErrors();
            bdump('TEST');
            return;
        }

        // VALIDATE TYPE
        if(!$this->validateType($values, $standardized)){
            $this->redrawErrors();
            bdump('VALIDATE TYPE ERROR');
            return;
        }

        // VALIDATE IF ALL CONDITIONS ARE SATISFIED
        $validateFields['conditions_valid'] = new ValidatorArgument($values->conditions_valid, 'isTrue', 'submit');
        $this->validator->validate($form, $validateFields);

        // REDRAW ERRORS
        $this->redrawErrors();
    }

    /**
     * @param array $data
     * @param int $problemId
     */
    public function handleCondValidation(array $data, int $problemId = null): void
    {
        $this->redrawControl('flashesSnippet');

        $values = ArrayHash::from($data);

        // VALIDATE BASE ITEMS
        if(!$this->validateBaseItems($values, true)){
            $this->redrawErrors(false);
            return;
        }

        // STANDARDIZE THE INPUT
        $standardized = $this->standardize($values);
        if($standardized === null){
            $this->redrawErrors(false);
            return;
        }

        // VALIDATE TYPE
        if(!$this->validateType($values, $standardized)){
            $this->redrawErrors(false);
            return;
        }

        // VALIDATE SPECIFIED CONDITION
        if(!$this->validateCondition($values, $standardized, $problemId)){
            $this->redrawErrors(false);
            return;
        }

        // REDRAW ERRORS
        $this->redrawErrors(false);

        // SEND PAYLOAD IF VALIDATION IS TRUE
        $this->flashMessage('Podmínka je splnitelná.', 'success');
        $this->redrawControl('flashesSnippet');
        $this->presenter->payload->result = true;
    }

    /**
     * @param ArrayHash $values
     * @return bool
     */
    public function validateBody(ArrayHash $values): bool
    {
        $validateFields['body'] = new ValidatorArgument($this->collectBodyValidationData($values), 'body_' . $values->type);

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
     * @param ArrayHash $values
     * @param $standardized
     * @return bool
     */
    public function validateType(ArrayHash $values, $standardized): bool
    {
        // Validate if the entered problem corresponds to the selected type
        $validateFields['type'] = new ValidatorArgument([
            'body' => $values->body,
            'standardized' => $standardized,
            'variable' => $values->variable
        ], 'type_' . $values->type, 'body');

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
     * @param ArrayHash $values
     * @param $standardized
     * @param int $problemId
     * @return bool
     */
    public function validateCondition(ArrayHash $values, $standardized, int $problemId = null): bool
    {
        $validationFields['condition_' . $values->conditionType] = new ValidatorArgument([
            'body' => $values->body,
            'standardized' => $standardized,
            'accessor' => $values->accessor,
            'variable' => $values->variable
            ],
            'condition_' . $values->conditionType, 'condition_' . $values->conditionType
        );

        // Validate template condition
        try{
            $form = $this->validator->conditionValidate($this['form'], $validationFields, $problemId ?? null);
        } catch (ProblemTemplateException $e){
            $this['form']['body']->addError($e->getMessage());
            return false;
        }

        if($form->hasErrors()){
            return false;
        }

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
    abstract public function standardize(ArrayHash $values);

    /**
     * @param ArrayHash $values
     * @return array
     */
    abstract public function collectBodyValidationData(ArrayHash $values): array;
}
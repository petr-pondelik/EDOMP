<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 17:10
 */

namespace App\Components\Forms\ProblemTemplateForm;


use App\Components\Forms\EntityFormControl;
use App\Exceptions\NewtonApiException;
use App\Exceptions\ProblemTemplateFormatException;
use App\Helpers\ConstHelper;
use App\Model\Functionality\BaseFunctionality;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Services\MathService;
use App\Services\ValidationService;
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
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $typeId;

    /**
     * @var array
     */
    protected $baseItems;

    /**
     * @var array
     */
    protected $baseItemsCondition;

    /**
     * ProblemTemplateFormControl constructor.
     * @param ValidationService $validationService
     * @param BaseFunctionality $functionality
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param MathService $mathService
     * @param ConstHelper $constHelper
     * @param bool $edit
     */
    public function __construct
    (
        ValidationService $validationService,
        BaseFunctionality $functionality,
        DifficultyRepository $difficultyRepository, ProblemTypeRepository $problemTypeRepository,
        SubCategoryRepository $subCategoryRepository, ProblemConditionRepository $problemConditionRepository,
        MathService $mathService, ConstHelper $constHelper,
        bool $edit = false
    )
    {
        parent::__construct($validationService, $edit);
        $this->functionality = $functionality;
        $this->difficultyRepository = $difficultyRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->mathService = $mathService;
        $this->constHelper = $constHelper;
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

        return $form;
    }

    /**
     * @param ArrayHash $values
     * @param bool $conditions
     * @return bool
     */
    public function validateBaseItems(ArrayHash $values, bool $conditions = false): bool
    {
        bdump('VALIDATE BASE ITEMS');
        if(!$conditions){
            foreach ($this->baseItems as $item){
                $validateFields[$item] = $values->{$item};
            }
        }
        else{
            foreach ($this->baseItemsCondition as $item){
                $validateFields[$item] = $values[$item];
            }
        }

        $validateFields['body'] = $this->collectBodyValidationData($values);

        try{
            $validationErrors = $this->validationService->validate($validateFields);
        } catch (\Exception $e){
            if($e instanceof NewtonApiException){
                $this['form']['submit']->addError($e->getMessage());
            }
            else{
                $this['form']['body']->addError($e->getMessage());
            }
            return false;
        }

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error){
                    $this['form'][$veKey]->addError($error);
                }
            }
            return false;
        }

        return true;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->getValues();

        bdump($values);

        // VALIDATE BASE ITEMS
        if(!$this->validateBaseItems($values)){
            $this->redrawFormErrors();
            return;
        }

        // STANDARDIZE THE INPUT
        if(!($standardized = $this->standardize($values))){
            $this->redrawFormErrors();
            return;
        }

        // VALIDATE TYPE
        if(!$this->validateType($values, $standardized)){
            return;
        }

        // VALIDATE IF ALL CONDITIONS ARE SATISFIED
        $validateFields['conditions_valid'] = $values->conditions_valid;
        $validationErrors = $this->validationService->validate($validateFields);

        if(isset($validationErrors['conditions_valid'])){
            foreach($validationErrors['conditions_valid'] as $error){
                $form['submit']->addError($error);
            }
        }

        // REDRAW ERRORS
        $this->redrawFormErrors();
    }

    /**
     * @param array $data
     * @param int $problemId
     */
    public function handleCondValidation(array $data, int $problemId = null): void
    {
        $values = ArrayHash::from($data);

        // VALIDATE BASE ITEMS
        if(!$this->validateBaseItems($values, true)){
            $this->redrawFormErrors();
            return;
        }

        // STANDARDIZE THE INPUT
        if(!($standardized = $this->standardize($values))){
            $this->redrawFormErrors();
            return;
        }

        // VALIDATE TYPE
        if(!$this->validateType($values, $standardized)){
            $this->redrawFormErrors();
            return;
        }

        // VALIDATE SPECIFIED CONDITION
        if(!$this->validateCondition($values, $standardized, $problemId)){
            $this->redrawFormErrors();
            return;
        }

        // REDRAW ERRORS
        $this->redrawFormErrors();

        // SEND PAYLOAD IF VALIDATION IS TRUE
        $this->flashMessage('Podmínka je splnitelná.', 'success');
        $this->presenter->payload->result = true;
    }

    /**
     * @param ArrayHash $values
     * @param $standardized
     * @return bool
     */
    public function validateType(ArrayHash $values, $standardized): bool
    {
        bdump('VALIDATE TYPE');
        bdump($values);
        // Validate if the entered problem corresponds to the selected type
        $validateFields['type'] = [
            'type_' . $values->type => ArrayHash::from([
                'body' => $values->body,
                'standardized' => $standardized,
                'variable' => $values->variable
            ])
        ];

        try{
            $validationErrors = $this->validationService->validate($validateFields);
        } catch (\Exception $e){
            $this['form']['body']->addError($e->getMessage());
            $this->redrawFormErrors();
            return false;
        }

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error){
                    $this['form']['body']->addError($error);
                }
            }
            $this->redrawFormErrors();
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
        $validationFields['condition'] = [
            'condition_' . $values->conditionType => ArrayHash::from([
                'body' => $values->body,
                'standardized' => $standardized,
                'accessor' => $values->accessor,
                'variable' => $values->variable
            ])
        ];

        // Validate template condition
        try{
            $validationErrors = $this->validationService->conditionValidate($validationFields, $problemId ?? null);
        } catch (ProblemTemplateFormatException $e){
            $this['form']['body']->addError($e->getMessage());
            return false;
        }

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error){
                    $this['form']['condition_' . $values->conditionType]->addError($error);
                }
            }
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
            $this->functionality->update($values->id_hidden, $values);
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
        $types = $this->problemTypeRepository->findAssoc([], 'id');
        $this->template->problemTypes = $types;
        $this->template->condByProblemTypes = [];
        foreach ($types as $key => $type){
            $this->template->condByProblemTypes[$key] = $type->getConditionTypes()->getValues();
        }

        if($this->edit){
            $this->template->render(__DIR__ . '/' . $this->type . '/templates/edit.latte');
        }
        else{
            $this->template->render(__DIR__ . '/' . $this->type . '/templates/create.latte');
        }
    }

    public function redrawFormErrors(): void
    {
        $this->redrawControl('variableErrorSnippet');
        $this->redrawControl('subCategoryErrorSnippet');
        $this->redrawControl('bodyErrorSnippet');
        $this->redrawControl('typeErrorSnippet');
        $this->redrawControl('difficultyErrorSnippet');
        $this->redrawControl('conditionsErrorSnippet');
        $this->redrawControl('firstNErrorSnippet');
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
     * @return ArrayHash
     */
    abstract public function collectBodyValidationData(ArrayHash $values): ArrayHash;
}
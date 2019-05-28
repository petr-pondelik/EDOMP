<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 17:10
 */

namespace App\Components\Forms\ProblemTemplateForm;


use App\Components\Forms\EntityFormControl;
use App\Exceptions\InvalidParameterException;
use App\Exceptions\StringFormatException;
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
class ProblemTemplateFormControl extends EntityFormControl
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
     * @var int
     */
    protected $templateType;

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
     * @param int $templateType
     * @param bool $edit
     */
    public function __construct
    (
        ValidationService $validationService,
        BaseFunctionality $functionality,
        DifficultyRepository $difficultyRepository, ProblemTypeRepository $problemTypeRepository,
        SubCategoryRepository $subCategoryRepository, ProblemConditionRepository $problemConditionRepository,
        MathService $mathService, ConstHelper $constHelper,
        int $templateType, bool $edit = false
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
        $this->templateType = $templateType;
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $difficulties = $this->difficultyRepository->findAssoc([], "id");
        $subcategories = $this->subCategoryRepository->findAssoc([], "id");

        $resultConditions = $this->problemConditionRepository->findAssoc([
            "problemConditionType.id" => $this->constHelper::RESULT
        ], "accessor");

        $discriminantConditions = $this->problemConditionRepository->findAssoc([
            "problemConditionType.id" => $this->constHelper::DISCRIMINANT
        ], "accessor");

        $form->addHidden("type")
            ->setDefaultValue($this->templateType);

        $form->addSelect("subcategory", "Podkategorie", $subcategories)
            ->setDefaultValue(1)
            ->setHtmlAttribute("class", "form-control");

        $form->addTextArea('text_before', 'Zadání před')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('before');

        $form->addTextArea('body', 'Tělo')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('body');

        $form->addText("variable", "Neznámá")
            ->setHtmlAttribute("class", "form-control")
            ->setHtmlId("variable");

        $form->addTextArea('text_after', 'Zadání po')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('after');

        $form->addSelect('difficulty', 'Obtížnost', $difficulties)
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('difficulty');

        if($this->templateType === $this->constHelper::ARITHMETIC_SEQ || $this->templateType === $this->constHelper::GEOMETRIC_SEQ){
            $form->addInteger('first_n', 'Prvních členů:')
                ->setHtmlAttribute('class', 'form-control')
                ->setHtmlId('first-n');
        }

        //Conditions
        $form->addSelect('condition_' . $this->constHelper::RESULT, 'Podmínka výsledku', $resultConditions)
            ->setHtmlAttribute('class', 'form-control condition')
            ->setHtmlId('condition-' . $this->constHelper::RESULT);

        $form->addSelect('condition_' . $this->constHelper::DISCRIMINANT, 'Podmínka diskriminantu', $discriminantConditions)
            ->setHtmlAttribute('class', 'form-control condition')
            ->setHtmlId('condition-' . $this->constHelper::DISCRIMINANT);

        //Field for storing all conditions final valid state
        $form->addHidden('conditions_valid')
            ->setDefaultValue(1)
            ->setHtmlId('conditions_valid');

        return $form;
    }

    /**
     * @param Form $form
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->getValues();

        //First validate problem body
        $validateFields["variable"] = $values->variable;

        if(in_array($this->templateType, $this->constHelper::SEQUENCES))
            $validateFields["first_n"] = $values->first_n;

        $validateFields["body"] = ArrayHash::from([
            "body" => $values->body,
            "variable" => $values->variable,
            "bodyType" => $this->constHelper::LINEAR_EQ
        ]);

        try{
            $validationErrors = $this->validationService->validate($validateFields);
        } catch (InvalidParameterException $e){
            $this["form"]["body"]->addError($e->getMessage());
            $this->redrawControl("bodyErrorSnippet");
            return;
        }

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $form[$veKey]->addError($error);
            }
            $this->redrawFormErrors();
            return;
        }

        $standardized = "";

        if(in_array($values->type, $this->constHelper::EQUATIONS)){
            try{
                $standardized = $this->mathService->standardizeEquation($values->body);
            } catch (StringFormatException $e){
                $form["body"]->addError($e->getMessage());
                $this->redrawFormErrors();
                return;
            }
        }

        $validateFields = [];

        //Then validate if the entered problem corresponds to the selected type
        $validateFields["type"] = [
            "type_" . $values->type => ArrayHash::from([
                "body" => $values->body,
                "standardized" => $standardized,
                "variable" => $values->variable
            ])
        ];

        $validationErrors = $this->validationService->validate($validateFields);

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $form["body"]->addError($error);
            }
            $this->redrawFormErrors();
            return;
        };

        $validateFields = [];

        //Then validate if all the conditions has been satisfied
        $validateFields["conditions_valid"] = $values->conditions_valid;
        $validationErrors = $this->validationService->validate($validateFields);

        if(isset($validationErrors['conditions_valid'])){
            foreach($validationErrors['conditions_valid'] as $error){
                $form['prototype_create_submit']->addError($error);
            }
        }

        $this->redrawFormErrors();
    }

    /**
     * @param string $body
     * @param int $conditionType
     * @param int $accessor
     * @param int $problemType
     * @param string $variable
     * @param int|null $problemId
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handleCondValidation(string $body, int $conditionType, int $accessor, int $problemType, string $variable, int $problemId = null)
    {
        $validationFields["variable"] = $variable;
        $validationFields['body'] = ArrayHash::from([
            "body" => $body,
            "bodyType" => $problemType,
            "variable" => $variable,
        ]);

        $validationErrors = $this->validationService->validate($validationFields);

        //First validate variable and structure of prototype
        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $this['form'][$veKey]->addError($error);
            }
            $this->redrawFormErrors();
            return;
        }

        try{
            $standardized = $this->mathService->standardizeEquation($body);
        } catch (StringFormatException $e){
            $this['form']["body"]->addError($e->getMessage());
            $this->redrawFormErrors();
            return;
        }

        $validationFields = [];

        //Then validate it's type
        $validationFields["type"] = [
            "type_" . $problemType => ArrayHash::from([
                "body" => $body,
                "standardized" => $standardized,
                "variable" => $variable
            ])
        ];

        $validationErrors = $this->validationService->validate($validationFields);

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $this['form']["body"]->addError($error);
            }
            $this->redrawFormErrors();
            return;
        }

        $validationFields = [];

        //Then validate specified condition
        $validationFields['condition'] = [
            'condition_' . $conditionType => ArrayHash::from([
                "body" => $body,
                "standardized" => $standardized,
                "accessor" => $accessor,
                "variable" => $variable
            ])
        ];

        if(!$problemId){
            //Validate on problem create
            $validationErrors = $this->validationService->validate($validationFields);
        }
        else{
            //Validate on problem edit
            $validationErrors = $this->validationService->editValidate($validationFields, $problemId);
        }

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $this['form']['condition_' . $conditionType]->addError($error);
            }
        }

        $this->redrawFormErrors();

        //If validation succeeded, return true in payload
        if(!$validationErrors){
            $this->flashMessage("Podmínka je splnitelná.", "success");
            $this->presenter->payload->result = true;
        }
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
            if ($e instanceof AbortException)
                return;
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
            if ($e instanceof AbortException)
                return;
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
        foreach ($types as $key => $type)
            $this->template->condByProblemTypes[$key] = $type->getConditionTypes()->getValues();
        if ($this->edit){
            switch ($this->templateType){
                case $this->constHelper::LINEAR_EQ: $this->template->render(__DIR__ . '/templates/linearEqEdit.latte');
                                                    break;
                case $this->constHelper::QUADRATIC_EQ: $this->template->render(__DIR__ . '/templates/quadraticEqEdit.latte');
                                                        break;
                case $this->constHelper::ARITHMETIC_SEQ: $this->template->render(__DIR__ . '/templates/arithmeticSeqEdit.latte');
                                                            break;
                case $this->constHelper::GEOMETRIC_SEQ: $this->template->render(__DIR__ . '/templates/geometricSeqEdit.latte');
                                                        break;
            }
        }
        else{
            switch ($this->templateType){
                case $this->constHelper::LINEAR_EQ: $this->template->render(__DIR__ . '/templates/linearEqCreate.latte');
                                                    break;
                case $this->constHelper::QUADRATIC_EQ: $this->template->render(__DIR__ . '/templates/quadraticEqCreate.latte');
                                                        break;
                case $this->constHelper::ARITHMETIC_SEQ: $this->template->render(__DIR__ . '/templates/arithmeticSeqCreate.latte');
                                                            break;
                case $this->constHelper::GEOMETRIC_SEQ: $this->template->render(__DIR__ . '/templates/geometricSeqCreate.latte');
                                                        break;
            }
        }
    }

    public function redrawFormErrors()
    {
        $this->redrawControl("variableErrorSnippet");
        $this->redrawControl('bodyErrorSnippet');
        $this->redrawControl("typeErrorSnippet");
        $this->redrawControl('conditionsErrorSnippet');
        $this->redrawControl("first_nErrorSnippet");
        $this->redrawControl("flashesSnippet");
        $this->redrawControl('submit');
    }
}
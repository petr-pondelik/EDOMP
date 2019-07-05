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
use App\Exceptions\NewtonApiException;
use App\Exceptions\ProblemTemplateFormatException;
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

//        $form->addText('variable', 'Neznámá *')
//            ->setHtmlAttribute('class', 'form-control')
//            ->setHtmlAttribute('placeholder', 'Neznámá šablony.')
//            ->setHtmlId('variable');

        $form->addTextArea('textAfter', 'Dodatek zadání')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Dodatečný text k zadání.')
            ->setHtmlId('after');

        $form->addSelect('difficulty', 'Obtížnost *', $difficulties)
            ->setPrompt('Zvolte obtížnost')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('difficulty');

//        if($this->templateType === $this->constHelper::ARITHMETIC_SEQ || $this->templateType === $this->constHelper::GEOMETRIC_SEQ){
//            $form->addInteger('first_n', 'Počet prvních členů *')
//                ->setHtmlAttribute('class', 'form-control')
//                ->setHtmlAttribute('placeholder', 'Zadejte počet zkoumaných prvních členů.')
//                ->setHtmlId('first-n');
//        }

        //Field for storing all conditions final valid state
        $form->addHidden('conditions_valid')
            ->setDefaultValue(1)
            ->setHtmlId('conditions_valid');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->getValues();

        $validateFields['variable'] = $values->variable;
        $validateFields['subCategory'] = $values->subCategory;
        $validateFields['difficulty'] = $values->difficulty;

//        if(in_array($this->templateType, $this->constHelper::SEQUENCES)){
//            $validateFields['first_n'] = $values->first_n;
//        }

        $validateFields['body'] = ArrayHash::from([
            'body' => $values->body,
            'variable' => $values->variable,
            'bodyType' => $this->constHelper::BODY_TEMPLATE
        ]);

        try{
            $validationErrors = $this->validationService->validate($validateFields);
        } catch (\Exception $e){
            if($e instanceof NewtonApiException){
                $this['form']['submit']->addError($e->getMessage());
            }
            else{
                $this['form']['body']->addError($e->getMessage());
            }
            $this->redrawFormErrors();
            return;
        }

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error){
                    $form[$veKey]->addError($error);
                }
            }
            $this->redrawFormErrors();
            return;
        }
//
//        // Then validate if the entered problem corresponds to the selected type
//        $validateFields['type'] = [
//            'type_' . $values->type => ArrayHash::from([
//                'body' => $values->body,
//                'standardized' => $standardized,
//                'variable' => $values->variable
//            ])
//        ];
//
//        try{
//            $validationErrors = $this->validationService->validate($validateFields);
//            bdump($validationErrors);
//        } catch (\Exception $e){
//            $form['body']->addError($e->getMessage());
//            $this->redrawFormErrors();
//            return;
//        }
//
//        if($validationErrors){
//            foreach($validationErrors as $veKey => $errorGroup){
//                foreach($errorGroup as $egKey => $error){
//                    $form['body']->addError($error);
//                }
//            }
//            $this->redrawFormErrors();
//            return;
//        }
//
        $validateFields = [];

        // Then validate if all the conditions has been satisfied
        $validateFields['conditions_valid'] = $values->conditions_valid;
        $validationErrors = $this->validationService->validate($validateFields);

        if(isset($validationErrors['conditions_valid'])){
            foreach($validationErrors['conditions_valid'] as $error){
                $form['submit']->addError($error);
            }
        }
//
//        $this->redrawFormErrors();
    }

    /**
     * @param string $body
     * @param int $conditionType
     * @param int $accessor
     * @param int $problemType
     * @param string $variable
     * @return bool
     */
    public function handleCondValidation(string $body, int $conditionType, int $accessor, int $problemType, string $variable)
    {
        $validationFields['variable'] = $variable;
        $validationFields['body'] = ArrayHash::from([
            'body' => $body,
            'bodyType' => $this->constHelper::BODY_TEMPLATE,
            'variable' => $variable,
        ]);

        try {
            $validationErrors = $this->validationService->validate($validationFields);
        } catch (\Exception $e){
            if($e instanceof NewtonApiException){
                $this['form']['submit']->addError($e->getMessage());
            }
            else{
                $this['form']['body']->addError($e->getMessage());
            }
            $this->redrawFormErrors();
            return false;
        }

        // First validate variable and body of template
        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error){
                    $this['form'][$veKey]->addError($error);
                }
            }
            $this->redrawFormErrors();
            return false;
        }

        return true;
    }

    /**
     * @param ArrayHash $values
     * @param string $standardized
     * @return bool
     */
    public function validateType(ArrayHash $values, string $standardized): bool
    {
        // Validate if the entered problem corresponds to the selected type
        $validateFields['type'] = [
            'type_' . $values->type => ArrayHash::from([
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
        $this->redrawControl('first_nErrorSnippet');
        $this->redrawControl('flashesSnippet');
        $this->redrawControl('submitErrorSnippet');
    }
}
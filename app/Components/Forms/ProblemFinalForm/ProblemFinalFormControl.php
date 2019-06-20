<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 14:50
 */

namespace App\Components\Forms\ProblemFinalForm;


use App\Components\Forms\EntityFormControl;
use App\Helpers\ConstHelper;
use App\Model\Functionality\ProblemFinalFunctionality;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Services\ValidationService;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemFinalFormControl
 * @package App\Components\Forms\ProblemFinalForm
 */
class ProblemFinalFormControl extends EntityFormControl
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
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * ProblemFinalFormControl constructor.
     * @param ValidationService $validationService
     * @param ProblemFinalFunctionality $problemFinalFunctionality
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param ConstHelper $constHelper
     * @param bool $edit
     */
    public function __construct
    (
        ValidationService $validationService,
        ProblemFinalFunctionality $problemFinalFunctionality,
        DifficultyRepository $difficultyRepository, ProblemTypeRepository $problemTypeRepository,
        SubCategoryRepository $subCategoryRepository, ProblemConditionRepository $problemConditionRepository,
        ConstHelper $constHelper,
        bool $edit = false
    )
    {
        parent::__construct($validationService, $edit);
        $this->functionality = $problemFinalFunctionality;
        $this->difficultyRepository = $difficultyRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->problemConditionRepository = $problemConditionRepository;
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
        $types = $this->problemTypeRepository->findAssoc([], 'id');
        $subcategories = $this->subCategoryRepository->findAssoc([], 'id');

        $resultConditions = $this->problemConditionRepository->findAssoc([
            'problemConditionType.id' => $this->constHelper::RESULT
        ], 'accessor');

        $discriminantConditions = $this->problemConditionRepository->findAssoc([
            'problemConditionType.id' => $this->constHelper::DISCRIMINANT
        ], 'accessor');

        $form->addHidden('is_generatable_hidden');

        $form->addSelect('problemFinalType', 'Typ *', $types)
            ->setPrompt('Zvolte typ úlohy')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('type');

        $form->addSelect('subCategory', 'Podkategorie *', $subcategories)
            ->setPrompt('Zvolte podkategorii')
            ->setHtmlAttribute('class', 'form-control');

        $form->addTextArea('text_before', 'Úvod zadání')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Úvodní text zadání.')
            ->setHtmlId('before');

        $form->addTextArea('body', 'Úloha *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder','Úloha určená k řešení.')
            ->setHtmlId('structure');

        $form->addText('variable', 'Neznámá')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('variable');

        $form->addTextArea('text_after', 'Dodatek k zadání')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Dodatečný text k zadání.')
            ->setHtmlId('after');

        $form->addSelect('difficulty', 'Obtížnost *', $difficulties)
            ->setPrompt('Zvolte obtížnost')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('difficulty');

        $form->addTextArea('result', 'Výsledek')
            ->setHtmlAttribute('placeholder', 'Výsledek úlohy.')
            ->setHtmlAttribute('class', 'form-control');

        //Conditions
        $form->addSelect('condition_' . $this->constHelper::RESULT, 'Podmínka výsledku', $resultConditions)
            ->setHtmlAttribute('class', 'form-control condition')
            ->setHtmlId('condition-' . $this->constHelper::RESULT);

        $form->addSelect('condition_' . $this->constHelper::DISCRIMINANT, 'Podmínka diskriminantu', $discriminantConditions)
            ->setHtmlAttribute('class', 'form-control condition')
            ->setHtmlId('condition-' . $this->constHelper::DISCRIMINANT);

        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->getValues();

        // First validate problem body, if it's not generated problem
        if(!$values->is_generatable_hidden){
            $validateFields['problemFinalType'] = $values->problemFinalType;
            $validateFields['body'] = ArrayHash::from([
                'body' => $values->body,
                'bodyType' => $this->constHelper::BODY_FINAL
            ]);
        }

        $validateFields['difficulty'] = $values->difficulty;
        $validateFields['subCategory'] = $values->subCategory;

        $validationErrors = $this->validationService->validate($validateFields);

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error){
                    $form[$veKey]->addError($error);
                }
            }
        }

        bdump($validationErrors);

        $this->redrawSnippets();
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
            //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
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
        if ($this->edit){
            $this->template->render(__DIR__ . '/templates/edit.latte');
        }
        else{
            $this->template->render(__DIR__ . '/templates/create.latte');
        }
    }

    public function redrawSnippets(): void
    {
        $this->redrawControl('flashesSnippet');
        $this->redrawControl('problemFinalTypeErrorSnippet');
        $this->redrawControl('subCategoryErrorSnippet');
        $this->redrawControl('bodyErrorSnippet');
        $this->redrawControl('difficultyErrorSnippet');
    }
}
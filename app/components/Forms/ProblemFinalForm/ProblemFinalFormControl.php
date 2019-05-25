<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 14:50
 */

namespace App\Components\Forms\ProblemFinalForm;


use App\Components\Forms\BaseFormControl;
use App\Helpers\ConstHelper;
use App\Model\Functionality\ProblemFinalFunctionality;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Service\ValidationService;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemFinalFormControl
 * @package App\Components\Forms\ProblemFinalForm
 */
class ProblemFinalFormControl extends BaseFormControl
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
     * @param bool $super
     */
    public function __construct
    (
        ValidationService $validationService,
        ProblemFinalFunctionality $problemFinalFunctionality,
        DifficultyRepository $difficultyRepository, ProblemTypeRepository $problemTypeRepository,
        SubCategoryRepository $subCategoryRepository, ProblemConditionRepository $problemConditionRepository,
        ConstHelper $constHelper,
        bool $edit = false, bool $super = false
    )
    {
        parent::__construct($validationService, $edit, $super);
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

        $difficulties = $this->difficultyRepository->findAssoc([], "id");
        $types = $this->problemTypeRepository->findAssoc([], "id");
        $subcategories = $this->subCategoryRepository->findAssoc([], "id");

        $resultConditions = $this->problemConditionRepository->findAssoc([
            "problemConditionType.id" => $this->constHelper::RESULT
        ], "accessor");

        $discriminantConditions = $this->problemConditionRepository->findAssoc([
            "problemConditionType.id" => $this->constHelper::DISCRIMINANT
        ], "accessor");

        $form->addSelect('type', 'Typ', $types)
            ->setDefaultValue(1)
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('type');

        $form->addSelect("subcategory", "Podkategorie", $subcategories)
            ->setDefaultValue(1)
            ->setHtmlAttribute("class", "form-control");

        $form->addTextArea('text_before', 'Zadání před')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('before');

        $form->addTextArea('body', 'Tělo')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('structure');

        $form->addText("variable", "Neznámá")
            ->setHtmlAttribute("class", "form-control")
            ->setHtmlId("variable");

        $form->addTextArea('text_after', 'Zadání po')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('after');

        $form->addSelect('difficulty', 'Obtížnost', $difficulties)
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('difficulty');

        $form->addTextArea('result', 'Výsledek')
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

        //First validate problem structure
        $validateFields["body"] = ArrayHash::from([
            "body" => $values->body,
            "bodyType" => $this->constHelper::BODY_FINAL
        ]);
        $validationErrors = $this->validationService->validate($validateFields);

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $form[$veKey]->addError($error);
                $this->redrawControl($veKey.'ErrorSnippet');
            }
        }

        $this->redrawControl("flashesSnippet");
        $this->redrawControl('bodyErrorSnippet');
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleCreateFormSuccess(Form $form, ArrayHash $values): void
    {
        try{
            $this->functionality->create($values);
            $this->onSuccess();
        } catch (\Exception $e){
            //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
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
        if ($this->edit)
            $this->template->render(__DIR__ . DIRECTORY_SEPARATOR . "edit.latte");
        else
            $this->template->render(__DIR__ . DIRECTORY_SEPARATOR . "create.latte");
    }
}
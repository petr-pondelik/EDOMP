<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 18:43
 */

namespace App\Components\Forms\SuperGroupForm;


use App\Components\Forms\BaseFormControl;
use App\Model\Functionality\SuperGroupFunctionality;
use App\Service\ValidationService;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class SuperGroupFormControl
 * @package App\Components\Forms\SuperGroupForm
 */
class SuperGroupFormControl extends BaseFormControl
{
    /**
     * @var SuperGroupFunctionality
     */
    protected $superGroupFunctionality;

    /**
     * SuperGroupFormControl constructor.
     * @param ValidationService $validationService
     * @param SuperGroupFunctionality $superGroupFunctionality
     * @param bool $edit
     */
    public function __construct
    (
        ValidationService $validationService,
        SuperGroupFunctionality $superGroupFunctionality,
        bool $edit = false
    )
    {
        parent::__construct($validationService, $edit);
        $this->superGroupFunctionality = $superGroupFunctionality;
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->addText("label", "Název")
                    ->setHtmlAttribute("class", "form-control");

        $form->addSubmit("submit", "Vytvořit")
            ->setHtmlAttribute("class", "btn btn-primary");

        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->values;

        $validateFields["label"] = $values->label;

        $validationErrors = $this->validationService->validate($validateFields);

        bdump($validationErrors);

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $form[$veKey]->addError($error);
            }
        }

        $this->redrawControl("labelErrorSnippet");
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleCreateFormSuccess(Form $form, ArrayHash $values): void
    {
        try{
            $this->superGroupFunctionality->create($values);
            $this->onSuccess();
        } catch (\Exception $e){
            //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if($e instanceof AbortException)
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
            $this->superGroupFunctionality->update($values->id_hidden, ArrayHash::from([
                "label" => $values->label
            ]));
            $this->onSuccess();
        } catch (\Exception $e){
            //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if($e instanceof AbortException)
                return;
            $this->onError($e);
        }
    }

    public function render(): void
    {
        if($this->edit)
            $this->template->render(__DIR__ . DIRECTORY_SEPARATOR . "editForm.latte");
        else
            $this->template->render(__DIR__ . DIRECTORY_SEPARATOR . "createForm.latte");
    }
}
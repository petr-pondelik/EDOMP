<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 11:54
 */

namespace App\Components\Forms\LogoForm;


use App\Components\Forms\BaseFormControl;
use App\Model\Functionality\LogoFunctionality;
use App\Service\FileService;
use App\Service\ValidationService;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class LogoFormControl
 * @package App\Components\Forms\LogoForm
 */
class LogoFormControl extends BaseFormControl
{
    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * LogoFormControl constructor.
     * @param ValidationService $validationService
     * @param LogoFunctionality $logoFunctionality
     * @param FileService $fileService
     * @param bool $edit
     */
    public function __construct
    (
        ValidationService $validationService, LogoFunctionality $logoFunctionality, FileService $fileService,
        bool $edit = false
    )
    {
        parent::__construct($validationService, $edit);
        $this->functionality = $logoFunctionality;
        $this->fileService = $fileService;
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->addText("label", "Název")
            ->setHtmlAttribute("class", "form-control");

        $form->addText("logo_file", "Soubor")
            ->setHtmlAttribute("class", "file-pond-input");

        $form->addSubmit("submit", "Vytvořit")
            ->setHtmlAttribute("class", "btn btn-primary");

        if($this->edit){
            $form->addSelect("edit_logo", "Editovat soubor", [
                0 => "Ne",
                1 => "Ano"
            ])
                ->setHtmlAttribute("class", "form-control mb-3");
        }

        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->values;

        $validateFields["label"] = $values->label;

        if($this->edit){
            if($values->edit_logo)
                $validateFields["logo_file"] = $values->logo_file;
        }
        else{
            $validateFields["logo_file"] = $values->logo_file;
        }

        $validationErrors = $this->validationService->validate($validateFields);

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $form[$veKey]->addError($error);
            }
        }

        $this->redrawControl("labelErrorSnippet");
        $this->redrawControl("logoFileErrorSnippet");
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleCreateFormSuccess(Form $form, ArrayHash $values): void
    {
        if($values->logo_file) {
            try{
                $this->fileService->finalStore($values->logo_file);
                $this->functionality->update($values->logo_file, ArrayHash::from([
                    "label" => $values->label
                ]));
                $this->onSuccess();
            } catch (\Exception $e){
                //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
                if ($e instanceof AbortException)
                    return;
                $this->onError();
            }
        }
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleEditFormSuccess(Form $form, ArrayHash $values): void
    {
        try{
            if($values->edit_logo && $values->logo_file){
                $this->fileService->finalStore($values->logo_file);
            }
            $this->functionality->update($values->id_hidden, ArrayHash::from([
                "label" => $values->label
            ]));
            $this->onSuccess();
        } catch (\Exception $e){
            //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException)
                return;
            $this->onError();
        }
    }

    public function render(): void
    {
        if ($this->edit)
            $this->template->render(__DIR__ . DIRECTORY_SEPARATOR . "edit.latte");
        else
            $this->template->render(__DIR__ . DIRECTORY_SEPARATOR . "create.latte");
    }
}
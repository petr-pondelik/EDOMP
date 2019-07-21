<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 11:54
 */

namespace App\Components\Forms\LogoForm;


use App\Arguments\ValidatorArgument;
use App\Components\Forms\EntityFormControl;
use App\Model\Functionality\LogoFunctionality;
use App\Services\FileService;
use App\Services\Validator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class LogoFormControl
 * @package App\Components\Forms\LogoForm
 */
class LogoFormControl extends EntityFormControl
{
    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * LogoFormControl constructor.
     * @param Validator $validator
     * @param LogoFunctionality $logoFunctionality
     * @param FileService $fileService
     * @param bool $edit
     */
    public function __construct
    (
        Validator $validator, LogoFunctionality $logoFunctionality, FileService $fileService,
        bool $edit = false
    )
    {
        parent::__construct($validator, $edit);
        $this->functionality = $logoFunctionality;
        $this->fileService = $fileService;
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->addText('label', 'Název *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte název loga.');

        $form->addText('logo', 'Soubor *')
            ->setHtmlAttribute('class', 'file-pond-input');

        if($this->edit){
            $form->addSelect('edit_logo', 'Editovat soubor', [
                0 => 'Ne',
                1 => 'Ano'
            ])
                ->setHtmlAttribute('class', 'form-control mb-3');
        }

        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->values;
        $validateFields['label'] = new ValidatorArgument($values->label, 'stringNotEmpty');
        if($this->edit){
            if($values->edit_logo){
                $validateFields['logo'] = new ValidatorArgument($values->logo, 'notEmpty');
            }
        }
        else{
            $validateFields['logo'] = new ValidatorArgument($values->logo, 'notEmpty');
        }
        $this->validator->validate($form, $validateFields);

//        if($validationErrors){
//            foreach($validationErrors as $veKey => $errorGroup){
//                foreach($errorGroup as $egKey => $error){
//                    $form[$veKey]->addError($error);
//                }
//            }
//        }
//
//        $this->redrawControl('labelErrorSnippet');
//        $this->redrawControl('logoErrorSnippet');
        $this->redrawErrors();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleFormSuccess(Form $form, ArrayHash $values): void
    {
        if($values->logo) {
            try{
                $this->fileService->finalStore($values->logo);
                $this->functionality->update($values->logo, ArrayHash::from([
                    'label' => $values->label
                ]));
                $this->onSuccess();
            } catch (\Exception $e){
                //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
                if ($e instanceof AbortException){
                    return;
                }
                $this->onError($e);
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
            if($values->edit_logo && $values->logo){
                $this->fileService->finalStore($values->logo);
            }
            $this->functionality->update($values->idHidden, ArrayHash::from([
                'label' => $values->label
            ]));
            $this->onSuccess();
        } catch (\Exception $e){
            //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException){
                return;
            }
            $this->onError($e);
        }
    }

    public function render(): void
    {
        if ($this->edit){
            $this->template->render(__DIR__ . '/templates/edit.latte');
        }
        else{
            $this->template->render(__DIR__ . '/templates/create.latte');
        }
    }
}
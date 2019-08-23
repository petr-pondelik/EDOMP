<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 18:43
 */

namespace App\Components\Forms\SuperGroupForm;


use App\Arguments\ValidatorArgument;
use App\Components\Forms\EntityFormControl;
use App\Model\Persistent\Functionality\SuperGroupFunctionality;
use App\Services\Validator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class SuperGroupFormControl
 * @package App\Components\Forms\SuperGroupForm
 */
class SuperGroupFormControl extends EntityFormControl
{
    /**
     * SuperGroupFormControl constructor.
     * @param Validator $validator
     * @param SuperGroupFunctionality $superGroupFunctionality
     * @param bool $edit
     */
    public function __construct
    (
        Validator $validator,
        SuperGroupFunctionality $superGroupFunctionality,
        bool $edit = false
    )
    {
        parent::__construct($validator, $edit);
        $this->functionality = $superGroupFunctionality;
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->addText('label', 'Název *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte název superskupiny.');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->values;
        $validateFields['label'] = new ValidatorArgument($values->label, 'stringNotEmpty');
        $this->validator->validate($form, $validateFields);
        $this->redrawErrors();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleFormSuccess(Form $form, ArrayHash $values): void
    {
        try{
            $values->user_id = $this->presenter->user->id;
            bdump($values->user_id);
            $this->functionality->create($values);
            $this->onSuccess();
        } catch (\Exception $e){
            //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if($e instanceof AbortException){
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
            $this->functionality->update($values->idHidden, ArrayHash::from([
                'label' => $values->label
            ]));
            $this->onSuccess();
        } catch (\Exception $e){
            //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if($e instanceof AbortException){
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
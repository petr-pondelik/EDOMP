<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.11.19
 * Time: 23:12
 */

namespace App\CoreModule\Components\Forms\ForgetPasswordForm;


use App\CoreModule\Arguments\ValidatorArgument;
use App\CoreModule\Components\Forms\FormControl;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class ForgetPasswordFormControl
 * @package App\CoreModule\Components\Forms\ForgetPasswordForm
 */
class ForgetPasswordFormControl extends FormControl
{
    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->addText('email', 'E-mail *')
            ->setHtmlAttribute('class', 'form-control');

        $form['submit']->caption = 'Zaslat nové heslo';

        return $form;
    }

    /**
     * @param Form $form
     * @throws \App\CoreModule\Exceptions\ValidatorException
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->getValues();
        $validationFields['email'] = new ValidatorArgument($values->email, 'email');
        $this->validator->validate($form, $validationFields);
        bdump($form->getErrors());
        $this->redrawErrors();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleFormSuccess(Form $form, ArrayHash $values): void
    {
        // TODO: Implement handleFormSuccess() method.
    }
}
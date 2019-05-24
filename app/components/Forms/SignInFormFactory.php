<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 20.3.19
 * Time: 21:02
 */

namespace App\Components\Forms;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class SignInFormFactory
 * @package app\components\forms
 */
class SignInFormFactory extends BaseFormControl
{
    /**
     * @return \Nette\Application\UI\Form
     */
    public function create()
    {
        $form = parent::create();

        $form->addText('username', 'Uživatelské jméno')
            ->setHtmlAttribute('class', 'form-control');

        $form->addPassword('password', 'Heslo')
            ->setHtmlAttribute('class', 'form-control');

        $form->addSubmit('signIn', 'Přihlásit se')
            ->setHtmlAttribute('class', 'btn btn-primary col-12 btn-lg');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        // TODO: Implement handleFormValidate() method.
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleCreateFormSuccess(Form $form, ArrayHash $values): void
    {
        // TODO: Implement handleCreateFormSuccess() method.
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleEditFormSuccess(Form $form, ArrayHash $values): void
    {
        // TODO: Implement handleEditFormSuccess() method.
    }

    public function render(): void
    {
        // TODO: Implement render() method.
    }
}
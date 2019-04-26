<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 20.3.19
 * Time: 21:02
 */

namespace App\Components\Forms;

/**
 * Class SignInFormFactory
 * @package app\components\forms
 */
class SignInFormFactory extends BaseForm
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
}
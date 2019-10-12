<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.5.19
 * Time: 17:00
 */

namespace App\Components\Forms\SignForm;


use App\Arguments\ValidatorArgument;
use App\Components\Forms\FormControl;
use App\Services\Validator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class SignFormControl
 * @package App\Components\Forms\SignForm
 */
class SignFormControl extends FormControl
{
    /**
     * @var bool
     */
    protected $admin;

    /**
     * SignFormControl constructor.
     * @param Validator $validator
     * @param bool $admin
     */
    public function __construct(Validator $validator, bool $admin = false)
    {
        parent::__construct($validator);
        $this->admin = $admin;
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();
        $form->addText('login', 'E-mail / Uživatelské jméno *')
            ->setHtmlAttribute('class', 'form-control');
        $form->addPassword('password', 'Heslo *')
            ->setHtmlAttribute('class', 'form-control');
        $form->addSubmit('signIn', 'Přihlásit se')
            ->setHtmlAttribute('class', 'btn btn-primary col-12 btn-lg');
        $form->onSuccess[] = [$this, 'handleFormSuccess'];
        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->getValues();
        $validateFields['login'] = new ValidatorArgument($values->login, 'login');
        $validateFields['password'] = new ValidatorArgument($values->password, 'notEmpty');
        $this->validator->validate($form, $validateFields);
        $this->redrawErrors();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws AbortException
     */
    public function handleFormSuccess(Form $form, ArrayHash $values): void
    {
        try {
            $this->presenter->user->login($values->login, $values->password, $this->admin);
            $this->onSuccess();
        } catch (\Exception $e) {
            bdump($e);
            if ($e instanceof AbortException) {
                throw $e;
            }
            bdump($e);
            $form['signIn']->addError($e->getMessage());
            $this->redrawControl('signInErrorSnippet');
            $this->onError($e);
        }
    }

    /**
     * @param string $login
     */
    public function setLogin(string $login): void
    {
        $this['form']['login']->setValue($login);
    }
}
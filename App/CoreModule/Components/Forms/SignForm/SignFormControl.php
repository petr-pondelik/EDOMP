<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.5.19
 * Time: 17:00
 */

namespace App\CoreModule\Components\Forms\SignForm;


use App\CoreModule\Arguments\ValidatorArgument;
use App\CoreModule\Components\Forms\FormControl;
use App\CoreModule\Services\Validator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class SignFormControl
 * @package App\CoreModule\Components\Forms\SignForm
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
        $signInButtonCaption = $this->presenter->isModule('Teacher') ? 'Přihlásit se jako učitel' : 'Přihlásit se jako student';
        $form = parent::createComponentForm();
        $form->addText('login', 'E-mail / Uživatelské jméno *')
            ->setHtmlAttribute('class', 'form-control');
        $form->addPassword('password', 'Heslo *')
            ->setHtmlAttribute('class', 'form-control');
        $form->addSubmit('signIn', $signInButtonCaption)
            ->setHtmlAttribute('class', 'btn btn-primary col-12 btn-lg');
        $form->onSuccess[] = [$this, 'handleFormSuccess'];
        return $form;
    }

    /**
     * @param Form $form
     * @throws \App\CoreModule\Exceptions\ValidatorException
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
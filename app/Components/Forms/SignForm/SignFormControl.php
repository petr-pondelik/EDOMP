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
        $form->addText('username', 'Uživatelské jméno')
            ->setHtmlAttribute('class', 'form-control');
        $form->addPassword('password', 'Heslo')
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
        $validateFields['username'] = new ValidatorArgument($values->username, 'notEmpty');
        $validateFields['password'] = new ValidatorArgument($values->username, 'notEmpty');
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
            $this->presenter->user->login($values->username, $values->password, $this->admin);
            $this->onSuccess();
        } catch(\Exception $e){
            if ($e instanceof AbortException){
                return;
            }
            $form['signIn']->addError($e->getMessage());
            $this->redrawControl('signInErrorSnippet');
            $this->onError($e);
        }
    }

    public function render(): void
    {
        $this->template->render(__DIR__ . '/templates/in.latte');
    }
}
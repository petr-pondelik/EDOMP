<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 20.3.19
 * Time: 19:15
 */

namespace App\Presenters;

use App\Components\Forms\SignInFormFactory;
use App\Services\Authenticator;
use App\Services\ValidationService;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Utils\ArrayHash;

/**
 * Class BaseSignPresenter
 * @package app\presenters
 */
class BaseSignPresenter extends BasePresenter
{

    /**
     * @var Authenticator
     */
    protected $authenticator;

    /**
     * @var ValidationService
     */
    protected $validationService;

    /**
     * @var SignInFormFactory
     */
    protected $signInFormFactory;

    /**
     * BaseSignPresenter constructor.
     * @param Authenticator $authenticator
     * @param ValidationService $validationService
     * @param SignInFormFactory $signInFormFactory
     */
    public function __construct
    (
        Authenticator $authenticator,
        ValidationService $validationService,
        SignInFormFactory $signInFormFactory
    )
    {
        parent::__construct();
        $this->authenticator = $authenticator;
        $this->validationService = $validationService;
        $this->signInFormFactory = $signInFormFactory;
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    public function createComponentSignInForm()
    {
        $form = $this->signInFormFactory->create();
        $form->onValidate[] = [$this, 'handleSignInValidate'];
        $form->onSuccess[] = [$this, 'handleSignInSuccess'];
        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleSignInValidate(Form $form)
    {
        $values = $form->getValues();

        $validateFields = [];
        foreach($values as $key => $value)
            $validateFields[$key] = $value;

        $validationErrors = $this->validationService->validate($validateFields);

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $form[$veKey]->addError($error);
            }
        }

        $this->redrawControl('usernameErrorSnippet');
        $this->redrawControl('passwordErrorSnippet');
        $this->redrawControl('signInErrorSnippet');
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Nette\Application\AbortException
     */
    public function handleSignInSuccess(Form $form, ArrayHash $values)
    {
        try{
            $this->user->login($values->username, $values->password);
            $this->redirect('Homepage:default');
        } catch(AuthenticationException $e){
            $this->flashMessage($e->getMessage(), "danger");
            //$form['signIn']->addError('Neplatné přihlašovací údaje.');
            //$this->redrawControl('signInErrorSnippet');
        }
    }

    public function handleLogout()
    {
        $this->user->logout(true);
    }
}
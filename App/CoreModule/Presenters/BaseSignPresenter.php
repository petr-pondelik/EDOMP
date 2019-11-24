<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 20.3.19
 * Time: 19:15
 */

namespace App\CoreModule\Presenters;

use App\CoreModule\Components\ForgetPassword\ForgetPasswordControl;
use App\CoreModule\Components\ForgetPassword\IForgetPasswordFactory;
use App\CoreModule\Components\Forms\SignForm\SignFormControl;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Components\Forms\SignForm\ISignFormFactory;
use App\CoreModule\Services\Authenticator;
use App\CoreModule\Services\Validator;

/**
 * Class BaseSignPresenter
 * @package App\CoreModule\Presenters
 */
abstract class BaseSignPresenter extends BasePresenter
{
    /**
     * @var Authenticator
     */
    protected $authenticator;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var ISignFormFactory
     */
    protected $signFormFactory;

    /**
     * @var IForgetPasswordFactory
     */
    protected $forgetPasswordFactory;

    /**
     * @var bool
     */
    protected $admin;

    /**
     * BaseSignPresenter constructor.
     * @param Authenticator $authenticator
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param Validator $validator
     * @param ISignFormFactory $signFormFactory
     * @param IForgetPasswordFactory $forgetPasswordFactory
     */
    public function __construct
    (
        Authenticator $authenticator,
        IHeaderBarFactory $headerBarFactory,
        ISideBarFactory $sideBarFactory,
        FlashesTranslator $flashesTranslator,
        Validator $validator,
        ISignFormFactory $signFormFactory,
        IForgetPasswordFactory $forgetPasswordFactory
    )
    {
        parent::__construct($headerBarFactory, $sideBarFactory, $flashesTranslator);
        $this->authenticator = $authenticator;
        $this->validator = $validator;
        $this->signFormFactory = $signFormFactory;
        $this->forgetPasswordFactory = $forgetPasswordFactory;
    }

    /**
     * @return SignFormControl
     */
    public function createComponentSignForm(): SignFormControl
    {
        $control = $this->signFormFactory->create($this->admin);
        $control->onSuccess[] = function () {
            if ($this->user->identity->firstName || $this->user->identity->lastName) {
                $this->flashMessage('Vítejte, ' . $this->user->identity->firstName . ' ' . $this->user->identity->lastName . '.');
            } else {
                $this->flashMessage('Vítejte, ' . $this->user->identity->username . '.');
            }
            $this->redirect('Homepage:default');
        };
        $control->onError[] = static function ($e) {
            bdump($e);
        };
        return $control;
    }

    /**
     * @return ForgetPasswordControl
     */
    public function createComponentForgetPassword(): ForgetPasswordControl
    {
        return $this->forgetPasswordFactory->create();
    }

    public function handleLogout(): void
    {
        $this->user->logout(true);
    }

    public function renderIn(): void
    {
        if ($this->user->isLoggedIn()) {
            $this->redirect('Homepage:default');
        }

        if($userEmail = $this->getParameter('email')){
            $this['signForm']->setLogin($userEmail);
        }
    }
}
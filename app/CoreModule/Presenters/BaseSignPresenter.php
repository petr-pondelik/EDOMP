<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 20.3.19
 * Time: 19:15
 */

namespace App\Presenters;

use App\CoreModule\Components\Forms\SignForm\SignFormControl;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Components\Forms\SignForm\ISignIFormFactory;
use App\Services\Authenticator;
use App\Services\Validator;

/**
 * Class BaseSignPresenter
 * @package app\presenters
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
     * @var ISignIFormFactory
     */
    protected $signFormFactory;

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
     * @param ISignIFormFactory $signFormFactory
     */
    public function __construct
    (
        Authenticator $authenticator,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        Validator $validator,
        ISignIFormFactory $signFormFactory
    )
    {
        parent::__construct($headerBarFactory, $sideBarFactory, $flashesTranslator);
        $this->authenticator = $authenticator;
        $this->validator = $validator;
        $this->signFormFactory = $signFormFactory;
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
            bdump('ON ERROR');
            bdump($e);
        };
        return $control;
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
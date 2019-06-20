<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 20.3.19
 * Time: 19:15
 */

namespace App\Presenters;

use App\Components\Forms\SignForm\SignFormControl;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\FlashesTranslator;
use App\Components\Forms\SignForm\SignFormFactory;
use App\Services\Authenticator;
use App\Services\ValidationService;

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
     * @var SignFormFactory
     */
    protected $signFormFactory;

    /**
     * BaseSignPresenter constructor.
     * @param Authenticator $authenticator
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param ValidationService $validationService
     * @param SignFormFactory $signFormFactory
     */
    public function __construct
    (
        Authenticator $authenticator,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        ValidationService $validationService,
        SignFormFactory $signFormFactory
    )
    {
        parent::__construct($headerBarFactory, $sideBarFactory, $flashesTranslator);
        $this->authenticator = $authenticator;
        $this->validationService = $validationService;
        $this->signFormFactory = $signFormFactory;
    }

    /**
     * @return SignFormControl
     */
    public function createComponentSignForm(): SignFormControl
    {
        $control = $this->signFormFactory->create();
        $control->onSuccess[] = function (){
            if($this->user->identity->firstName || $this->user->identity->lastName){
                $this->flashMessage('Vítejte, ' . $this->user->identity->firstName . ' ' . $this->user->identity->lastName . '.');
            }
            else{
                $this->flashMessage('Vítejte, ' . $this->user->identity->username . '.');
            }
            $this->redirect('Homepage:default');
        };
        $control->onError[] = function ($e){};
        return $control;
    }

    public function handleLogout()
    {
        $this->user->logout(true);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 2.10.19
 * Time: 19:34
 */

namespace App\FrontModule\Presenters;

use App\Arguments\UserInformArgs;
use App\Components\Forms\PasswordForm\IPasswordFormFactory;
use App\Components\Forms\PasswordForm\PasswordFormControl;
use App\Components\HeaderBar\IHeaderBarFactory;
use App\Components\SideBar\ISideBarFactory;
use App\Helpers\FlashesTranslator;
use App\Services\Authorizator;

/**
 * Class SettingsPresenter
 * @package App\FrontModule\Presenters
 */
class SettingsPresenter extends FrontPresenter
{
    /**
     * @var IPasswordFormFactory
     */
    protected $passwordFormFactory;

    /**
     * SettingsPresenter constructor.
     * @param Authorizator $authorizator
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param IPasswordFormFactory $passwordFormFactory
     */
    public function __construct
    (
        Authorizator $authorizator,
        IHeaderBarFactory $headerBarFactory,
        ISideBarFactory $sideBarFactory,
        FlashesTranslator $flashesTranslator,
        IPasswordFormFactory $passwordFormFactory
    )
    {
        parent::__construct($authorizator, $headerBarFactory, $sideBarFactory, $flashesTranslator);
        $this->passwordFormFactory = $passwordFormFactory;
    }

    /**
     * @return PasswordFormControl
     */
    public function createComponentPasswordForm(): PasswordFormControl
    {
        $control = $this->passwordFormFactory->create();
        $control->onSuccess[] = function () {
            $this->informUser(new UserInformArgs($this->getAction(), true, 'success', null,false,'passwordForm'));
        };
        $control->onError[] = function (\Exception $e) {
            $this->informUser(new UserInformArgs($this->getAction(), true, 'danger', $e, false, 'passwordForm'));
        };
        return $control;
    }
}
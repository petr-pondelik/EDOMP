<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 2.10.19
 * Time: 19:34
 */

namespace App\StudentModule\Presenters;

use App\CoreModule\Arguments\UserInformArgs;
use App\CoreModule\Components\Forms\PasswordForm\IPasswordFormFactory;
use App\CoreModule\Components\Forms\PasswordForm\PasswordFormControl;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Services\Authorizator;

/**
 * Class SettingsPresenter
 * @package App\StudentModule\Presenters
 */
class SettingsPresenter extends StudentPresenter
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
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 17:24
 */

namespace App\TeacherModule\Presenters;


use App\CoreModule\Components\ForgetPassword\ForgetPasswordControl;
use App\CoreModule\Components\ForgetPassword\IForgetPasswordFactory;
use App\CoreModule\Components\Forms\SignForm\ISignFormFactory;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Presenters\BaseSignPresenter;
use App\CoreModule\Services\Authenticator;
use App\CoreModule\Services\Validator;

/**
 * Class SignPresenter
 * @package App\TeacherModule\Presenters
 */
class SignPresenter extends BaseSignPresenter
{
    /**
     * @var IForgetPasswordFactory
     */
    protected $forgetPasswordFactory;

    /**
     * @var bool
     */
    protected $admin = true;

    /**
     * SignPresenter constructor.
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
        parent::__construct($authenticator, $headerBarFactory, $sideBarFactory, $flashesTranslator, $validator, $signFormFactory);
        $this->forgetPasswordFactory = $forgetPasswordFactory;
    }

    /**
     * @return ForgetPasswordControl
     */
    public function createComponentForgetPassword(): ForgetPasswordControl
    {
        return $this->forgetPasswordFactory->create();
    }
}
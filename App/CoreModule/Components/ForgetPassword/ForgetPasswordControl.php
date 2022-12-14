<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.11.19
 * Time: 22:16
 */

namespace App\CoreModule\Components\ForgetPassword;

use App\CoreModule\Components\EDOMPControl;
use App\CoreModule\Components\Forms\ForgetPasswordForm\ForgetPasswordFormControl;
use App\CoreModule\Components\Forms\ForgetPasswordForm\IForgetPasswordFormFactory;

/**
 * Class ForgetPasswordControl
 * @package App\CoreModule\Components\ForgetPassword
 */
class ForgetPasswordControl extends EDOMPControl
{
    /**
     * @var IForgetPasswordFormFactory
     */
    protected $forgetPasswordFormFactory;

    /**
     * ForgetPassword constructor.
     * @param IForgetPasswordFormFactory $forgetPasswordFormFactory
     */
    public function __construct(IForgetPasswordFormFactory $forgetPasswordFormFactory)
    {
        parent::__construct();
        $this->forgetPasswordFormFactory = $forgetPasswordFormFactory;
    }

    /**
     * @return ForgetPasswordFormControl
     */
    public function createComponentForgetPasswordForm(): ForgetPasswordFormControl
    {
        return $this->forgetPasswordFormFactory->create();
    }

    public function render(): void
    {
        $this->template->size = 'lg';
        $this->template->labelItem = 'forget-password-modal-label';
        $this->template->id = 'forget-password-modal';
        parent::render();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.11.19
 * Time: 23:14
 */

namespace App\CoreModule\Components\Forms\ForgetPasswordForm;

use App\CoreModule\Components\Forms\IFormFactory;

/**
 * Interface IForgetPasswordFormFactory
 * @package App\CoreModule\Components\Forms\ForgetPasswordForm
 */
interface IForgetPasswordFormFactory extends IFormFactory
{
    /**
     * @return ForgetPasswordFormControl
     */
    public function create(): ForgetPasswordFormControl;
}
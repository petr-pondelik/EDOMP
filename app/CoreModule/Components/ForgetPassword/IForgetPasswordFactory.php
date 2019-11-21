<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.11.19
 * Time: 22:18
 */

namespace App\CoreModule\Components\ForgetPassword;

/**
 * Interface IForgetPasswordFactory
 * @package App\CoreModule\Components\ForgetPassword
 */
interface IForgetPasswordFactory
{
    /**
     * @return ForgetPasswordControl
     */
    public function create(): ForgetPasswordControl;
}
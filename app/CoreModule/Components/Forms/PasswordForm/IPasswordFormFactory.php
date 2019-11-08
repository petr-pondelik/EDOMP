<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 2.10.19
 * Time: 19:52
 */

namespace App\CoreModule\Components\Forms\PasswordForm;

/**
 * Interface IPasswordFormFactory
 * @package App\CoreModule\Components\Forms\PasswordForm
 */
interface IPasswordFormFactory
{
    /**
     * @return PasswordFormControl
     */
    public function create(): PasswordFormControl;
}
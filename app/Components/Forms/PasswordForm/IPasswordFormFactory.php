<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 2.10.19
 * Time: 19:52
 */

namespace App\Components\Forms\PasswordForm;

/**
 * Interface IPasswordFormFactory
 * @package App\Components\Forms\PasswordForm
 */
interface IPasswordFormFactory
{
    /**
     * @return PasswordFormControl
     */
    public function create(): PasswordFormControl;
}
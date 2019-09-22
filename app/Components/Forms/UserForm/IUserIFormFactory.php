<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 10:38
 */

namespace App\Components\Forms\UserForm;


use App\Components\Forms\IFormFactory;

/**
 * Interface IUserIFormFactory
 * @package App\Components\Forms\UserForm
 */
interface IUserIFormFactory extends IFormFactory
{
    /**
     * @return UserFormControl
     */
    public function create(): UserFormControl;
}
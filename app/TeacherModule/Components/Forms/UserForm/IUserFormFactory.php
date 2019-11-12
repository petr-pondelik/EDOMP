<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 10:38
 */

namespace App\TeacherModule\Components\Forms\UserForm;


use App\CoreModule\Components\Forms\IFormFactory;

/**
 * Interface IUserFormFactory
 * @package App\TeacherModule\Components\Forms\UserForm
 */
interface IUserFormFactory extends IFormFactory
{
    /**
     * @return UserFormControl
     */
    public function create(): UserFormControl;
}
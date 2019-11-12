<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 12:40
 */

namespace App\TeacherModule\Components\Forms\PermissionForm;


use App\CoreModule\Components\Forms\IFormFactory;

/**
 * Interface IPermissionIFormFactory
 * @package App\TeacherModule\Components\Forms\PermissionForm
 */
interface IPermissionFormFactory extends IFormFactory
{
    public function create(bool $super = false): PermissionFormControl;
}
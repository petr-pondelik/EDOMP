<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 15:04
 */

namespace App\TeacherModule\Components\Forms\SubThemeForm;


use App\CoreModule\Components\Forms\IFormFactory;

/**
 * Class SubThemeFormControlFactory
 * @package App\TeacherModule\Components\Forms\SubThemeForm
 */
interface ISubThemeFormFactory extends IFormFactory
{
    public function create(): SubThemeFormControl;
}
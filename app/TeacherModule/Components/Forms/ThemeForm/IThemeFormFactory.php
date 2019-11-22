<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 11:26
 */

namespace App\TeacherModule\Components\Forms\ThemeForm;


use App\CoreModule\Components\Forms\IFormFactory;

/**
 * Interface IThemeFormFactory
 * @package App\TeacherModule\Components\Forms\ThemeForm
 */
interface IThemeFormFactory extends IFormFactory
{
    public function create(): ThemeFormControl;
}
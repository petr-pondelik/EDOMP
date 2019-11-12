<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 15:04
 */

namespace App\TeacherModule\Components\Forms\SubCategoryForm;


use App\CoreModule\Components\Forms\IFormFactory;

/**
 * Class SubCategoryFormControlFactory
 * @package App\TeacherModule\Components\Forms\SubCategoryForm
 */
interface ISubCategoryFormFactory extends IFormFactory
{
    public function create(): SubCategoryFormControl;
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 11:26
 */

namespace App\TeacherModule\Components\Forms\CategoryForm;


use App\CoreModule\Components\Forms\IFormFactory;

/**
 * Interface ICategoryFormFactory
 * @package App\TeacherModule\Components\Forms\CategoryForm
 */
interface ICategoryFormFactory extends IFormFactory
{
    public function create(): CategoryFormControl;
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 15:04
 */

namespace App\Components\Forms\SubCategoryForm;


use App\Components\Forms\IFormFactory;

/**
 * Class SubCategoryFormControlFactory
 * @package App\Components\Forms\SubCategoryForm
 */
interface ISubCategoryFormFactory extends IFormFactory
{
    public function create(): SubCategoryFormControl;
}
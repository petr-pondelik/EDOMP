<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 11:26
 */

namespace App\Components\Forms\CategoryForm;


use App\Components\Forms\IFormFactory;

/**
 * Interface ICategoryFormFactory
 * @package App\Components\Forms\CategoryForm
 */
interface ICategoryFormFactory extends IFormFactory
{
    public function create(): CategoryFormControl;
}
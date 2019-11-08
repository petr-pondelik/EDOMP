<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.9.19
 * Time: 11:20
 */

namespace App\Components\Forms\TestForm\TestEntityForm;


/**
 * Interface ITestEntityFormFactory
 * @package App\Components\Forms\TestForm\TestEntityForm
 */
interface ITestFormFactory
{
    public function create(): TestFormControl;
}
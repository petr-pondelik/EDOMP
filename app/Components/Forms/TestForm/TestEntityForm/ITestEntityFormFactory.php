<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.9.19
 * Time: 11:20
 */

namespace App\Components\Forms\TestForm\TestEntityForm;


use App\Components\Forms\TestForm\ITestFormFactory;

/**
 * Interface ITestEntityFormFactory
 * @package App\Components\Forms\TestForm\TestEntityForm
 */
interface ITestEntityFormFactory extends ITestFormFactory
{
    public function create(): TestEntityFormControl;
}
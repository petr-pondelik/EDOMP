<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.7.19
 * Time: 13:27
 */

namespace App\Components\Forms\TestForm;

/**
 * Interface ITestFormFactory
 * @package App\Components\Forms\TestForm
 */
interface ITestFormFactory
{
    /**
     * @return TestFormControl
     */
    public function create(): TestFormControl;
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.9.19
 * Time: 11:20
 */

namespace App\TeacherModule\Components\Forms\TestForm;


/**
 * Interface ITestEntityFormFactory
 * @package App\TeacherModule\Components\Forms\TestForm
 */
interface ITestFormFactory
{
    public function create(): TestFormControl;
}
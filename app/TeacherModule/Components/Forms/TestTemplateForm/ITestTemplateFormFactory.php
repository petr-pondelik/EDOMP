<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.10.19
 * Time: 16:45
 */

namespace App\Components\Forms\TestTemplateForm;

/**
 * Interface ITestTemplateFormFactory
 * @package App\Components\Forms\TestTemplateForm
 */
interface ITestTemplateFormFactory
{
    /**
     * @return TestTemplateFormControl
     */
    public function create(): TestTemplateFormControl;
}
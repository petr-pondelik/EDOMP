<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:20
 */

namespace App\Components\Forms\ProblemTemplateForm\LinearEqTemplateForm;


use App\Components\Forms\ProblemTemplateForm\IProblemTemplateFormFactory;

/**
 * Interface ILinearEqTemplateFormFactory
 * @package App\Components\Forms\ProblemTemplateForm\LinearEqTemplateForm
 */
interface ILinearEqTemplateFormFactory extends IProblemTemplateFormFactory
{
    /**
     * @return LinearEqTemplateFormControl
     */
    public function create(): LinearEqTemplateFormControl;
}
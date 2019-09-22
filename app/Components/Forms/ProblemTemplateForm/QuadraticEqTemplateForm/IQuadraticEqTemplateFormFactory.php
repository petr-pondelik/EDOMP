<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:49
 */

namespace App\Components\Forms\ProblemTemplateForm\QuadraticEqTemplateForm;


use App\Components\Forms\ProblemTemplateForm\IProblemTemplateFormFactory;

/**
 * Interface IQuadraticEqTemplateFormFactory
 * @package App\Components\Forms\ProblemTemplateForm\QuadraticEqTemplateForm
 */
interface IQuadraticEqTemplateFormFactory extends IProblemTemplateFormFactory
{
    /**
     * @return QuadraticEqTemplateFormControl
     */
    public function create(): QuadraticEqTemplateFormControl;
}
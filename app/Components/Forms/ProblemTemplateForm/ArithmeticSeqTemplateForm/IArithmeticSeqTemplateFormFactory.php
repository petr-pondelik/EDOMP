<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:58
 */

namespace App\Components\Forms\ProblemTemplateForm\ArithmeticSeqTemplateForm;

use App\Components\Forms\ProblemTemplateForm\IProblemTemplateFormFactory;

/**
 * Interface IArithmeticSeqTemplateFormFactory
 * @package App\Components\Forms\ProblemTemplateForm\ArithmeticSeqTemplateForm
 */
interface IArithmeticSeqTemplateFormFactory extends IProblemTemplateFormFactory
{
    /**
     * @return ArithmeticSeqTemplateFormControl
     */
    public function create(): ArithmeticSeqTemplateFormControl;
}
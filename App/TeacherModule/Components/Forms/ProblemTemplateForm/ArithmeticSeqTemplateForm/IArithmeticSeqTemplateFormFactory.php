<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:58
 */

namespace App\TeacherModule\Components\Forms\ProblemTemplateForm\ArithmeticSeqTemplateForm;

use App\TeacherModule\Components\Forms\ProblemTemplateForm\IProblemTemplateFormFactory;

/**
 * Interface IArithmeticSeqTemplateFormFactory
 * @package App\TeacherModule\Components\Forms\ProblemTemplateForm\ArithmeticSeqTemplateForm
 */
interface IArithmeticSeqTemplateFormFactory extends IProblemTemplateFormFactory
{
    /**
     * @return ArithmeticSeqTemplateFormControl
     */
    public function create(): ArithmeticSeqTemplateFormControl;
}
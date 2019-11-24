<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:56
 */

namespace App\TeacherModule\Components\Forms\ProblemTemplateForm\GeometricSeqTemplateForm;

use App\TeacherModule\Components\Forms\ProblemTemplateForm\IProblemTemplateFormFactory;

/**
 * Interface IGeometricSeqTemplateFormFactory
 * @package App\TeacherModule\Components\Forms\ProblemTemplateForm\GeometricSeqTemplateForm
 */
interface IGeometricSeqTemplateFormFactory extends IProblemTemplateFormFactory
{
    /**
     * @return GeometricSeqTemplateFormControl
     */
    public function create(): GeometricSeqTemplateFormControl;
}
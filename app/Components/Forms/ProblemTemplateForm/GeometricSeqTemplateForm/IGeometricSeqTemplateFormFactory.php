<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:56
 */

namespace ProblemTemplateForm\GeometricSeqTemplateForm;


use App\Components\Forms\ProblemTemplateForm\IProblemTemplateFormFactory;

/**
 * Interface IGeometricSeqTemplateFormFactory
 * @package ProblemTemplateForm\GeometricSeqTemplateForm
 */
interface IGeometricSeqTemplateFormFactory extends IProblemTemplateFormFactory
{
    /**
     * @return GeometricSeqTemplateFormControl
     */
    public function create(): GeometricSeqTemplateFormControl;
}
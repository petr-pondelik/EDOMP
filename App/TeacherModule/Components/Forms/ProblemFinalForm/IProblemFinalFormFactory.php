<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 14:51
 */

namespace App\TeacherModule\Components\Forms\ProblemFinalForm;


use App\CoreModule\Components\Forms\IFormFactory;

/**
 * Class IProblemFinalFormFactory
 * @package App\TeacherModule\Components\Forms\ProblemFinalForm
 */
interface IProblemFinalFormFactory extends IFormFactory
{
    /**
     * @return ProblemFinalFormControl
     */
    public function create(): ProblemFinalFormControl;
}
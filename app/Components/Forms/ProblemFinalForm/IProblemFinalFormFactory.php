<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 14:51
 */

namespace App\Components\Forms\ProblemFinalForm;


use App\Components\Forms\IFormFactory;

/**
 * Class IProblemFinalFormFactory
 * @package App\Components\Forms\ProblemFinalForm
 */
interface IProblemFinalFormFactory extends IFormFactory
{
    /**
     * @return ProblemFinalFormControl
     */
    public function create(): ProblemFinalFormControl;
}
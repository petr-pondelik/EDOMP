<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 31.5.19
 * Time: 21:58
 */

namespace App\Components\Forms\ProblemTypeForm;


use App\Components\Forms\IFormFactory;

/**
 * Interface IProblemTypeFormFactory
 * @package App\Components\Forms\ProblemTypeForm
 */
interface IProblemTypeFormFactory extends IFormFactory
{
    /**
     * @return ProblemTypeFormControl
     */
    public function create(): ProblemTypeFormControl;
}
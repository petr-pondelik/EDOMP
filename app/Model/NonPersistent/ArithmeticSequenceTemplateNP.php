<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.8.19
 * Time: 21:49
 */

namespace App\Model\NonPersistent;

/**
 * Class ArithmeticSequenceTemplate
 * @package App\Model\NonPersistent
 */
class ArithmeticSequenceTemplateNP extends ProblemTemplateNP
{
    /**
     * @var string
     */
    public $variable;

    /**
     * @var int
     */
    public $firstN;
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.8.19
 * Time: 21:50
 */

namespace App\Model\NonPersistent;

/**
 * Class GeometricSequenceTemplate
 * @package App\Model\NonPersistent
 */
class GeometricSequenceTemplateNP extends ProblemTemplateNP
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
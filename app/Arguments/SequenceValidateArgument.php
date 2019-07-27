<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.7.19
 * Time: 8:10
 */

namespace App\Arguments;

use Nette\Utils\ArrayHash;

/**
 * Class SequenceTemplateValidateArgument
 * @package App\Arguments
 */
class SequenceValidateArgument extends ProblemValidateArgument
{
    /**
     * @var string
     */
    public $expression;

    /**
     * @var string
     */
    public $standardized;

    /**
     * @var string
     */
    public $variable;

    /**
     * SequenceTemplateValidateArgument constructor.
     * @param ArrayHash $data
     */
    public function __construct(ArrayHash $data)
    {
        $this->expression = $data->body;
        $this->standardized = $data->standardized;
        $this->variable = $data->variable;
    }
}
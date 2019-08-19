<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.7.19
 * Time: 7:54
 */

namespace App\Arguments;

use Nette\Utils\ArrayHash;

/**
 * Class EquationTemplateValidateArgument
 * @package App\Arguments
 */
class EquationValidateArgument extends ProblemValidateArgument
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
     * EquationTemplateValidateArgument constructor.
     * @param ArrayHash $data
     */
    public function __construct(ArrayHash $data)
    {
        parent::__construct($data);
        $this->expression = $data->body;
        $this->standardized = $data->standardized;
        $this->variable = $data->variable;
    }
}
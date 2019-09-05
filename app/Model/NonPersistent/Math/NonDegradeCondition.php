<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.8.19
 * Time: 12:27
 */

namespace App\Model\NonPersistent\Math;

use Nette\Utils\Strings;

/**
 * Class NonDegradeCondition
 * @package App\Model\NonPersistent\Math
 */
class NonDegradeCondition
{
    /**
     * @var string
     */
    protected $expression;

    /**
     * @var string
     */
    protected $variable;

    /**
     * @var bool
     */
    protected $parametrized;

    /**
     * NonDegradeCondition constructor.
     * @param string $expression
     * @param string $variable
     */
    public function __construct(string $expression, string $variable)
    {
        $this->expression = $expression;
        $this->variable = $variable;
        Strings::match($expression, '~p\d+~') ? $this->parametrized = true : $this->parametrized = false;
    }

    /**
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * @param string $expression
     */
    public function setExpression(string $expression): void
    {
        $this->expression = $expression;
        Strings::match($expression, '~p\d+~') ? $this->parametrized = true : $this->parametrized = false;
    }

    /**
     * @return string
     */
    public function getVariable(): string
    {
        return $this->variable;
    }

    /**
     * @return bool
     */
    public function isParametrized(): bool
    {
        return $this->parametrized;
    }

    /**
     * @param bool $parametrized
     */
    public function setParametrized(bool $parametrized): void
    {
        $this->parametrized = $parametrized;
    }
}
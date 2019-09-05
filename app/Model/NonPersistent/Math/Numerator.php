<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 31.8.19
 * Time: 21:19
 */

namespace App\Model\NonPersistent\Math;

use Nette\Utils\Strings;

/**
 * Class Numerator
 * @package App\Model\NonPersistent\Math
 */
class Numerator
{
    /**
     * @var string
     */
    protected $expression;

    /**
     * @var bool
     */
    protected $parametrized;

    /**
     * Numerator constructor.
     * @param string $expression
     */
    public function __construct(string $expression)
    {
        $this->expression = $expression;
        Strings::match($expression, '~p\d+~') ? $this->parametrized = true : $this->parametrized = false;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->expression;
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
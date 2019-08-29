<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.8.19
 * Time: 21:04
 */

namespace App\Model\NonPersistent\Math;

use App\Model\NonPersistent\Traits\SetValuesTrait;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class VariableFraction
 * @package App\Model\NonPersistent\Math
 */
class VariableFraction
{
    use SetValuesTrait;

    /**
     * @var string
     */
    protected $expression;

    /**
     * @var string
     */
    protected $numerator;

    /**
     * @var string
     */
    protected $divider;

    /**
     * @var int
     */
    protected $coefficient;

    /**
     * @var array
     */
    protected $factors;

    /**
     * @var bool
     */
    protected $parametrized;

    /**
     * VariableFraction constructor.
     * @param ArrayHash $data
     */
    public function __construct(ArrayHash $data)
    {
        $this->setValues($data);
    }

    /**
     * @return bool
     */
    public function hasParameters(): bool
    {
        return Strings::match($this->numerator, '~p\d+~') || Strings::match($this->divider, '~p\d+~');
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
     * @return int
     */
    public function getCoefficient(): int
    {
        return $this->coefficient;
    }

    /**
     * @param int $coefficient
     */
    public function setCoefficient(int $coefficient): void
    {
        $this->coefficient = $coefficient;
    }

    /**
     * @return array
     */
    public function getFactors(): array
    {
        return $this->factors;
    }

    /**
     * @param array $factors
     */
    public function setFactors(array $factors): void
    {
        $this->factors = $factors;
    }

    /**
     * @return string
     */
    public function getNumerator(): string
    {
        return $this->numerator;
    }

    /**
     * @param string $numerator
     */
    public function setNumerator(string $numerator): void
    {
        $this->numerator = $numerator;
    }

    /**
     * @return string
     */
    public function getDivider(): string
    {
        return $this->divider;
    }

    /**
     * @param string $divider
     */
    public function setDivider(string $divider): void
    {
        $this->divider = $divider;
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
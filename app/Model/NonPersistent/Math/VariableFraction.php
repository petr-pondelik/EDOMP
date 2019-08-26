<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.8.19
 * Time: 21:04
 */

namespace App\Model\NonPersistent\Math;

/**
 * Class VariableFraction
 * @package App\Model\NonPersistent\Math
 */
class VariableFraction
{
    /**
     * @var string
     */
    protected $expression;

    /**
     * @var string
     */
    protected $operator;

    /**
     * @var string
     */
    protected $factor;

    /**
     * @var int
     */
    protected $coefficient;

    /**
     * @var array
     */
    protected $factors;

    /**
     * VariableFraction constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        bdump($data);
        $this->expression = $data[0];
        $this->operator = $data[1];
        $this->factor = $data[2];
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
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @param string $operator
     */
    public function setOperator(string $operator): void
    {
        $this->operator = $operator;
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
    public function getFactor(): string
    {
        return $this->factor;
    }

    /**
     * @param string $factor
     */
    public function setFactor(string $factor): void
    {
        $this->factor = $factor;
    }
}
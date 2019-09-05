<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.8.19
 * Time: 21:04
 */

namespace App\Model\NonPersistent\Math;

use App\Model\NonPersistent\Traits\SetValuesTrait;
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
     * @var Numerator
     */
    protected $numerator;

    /**
     * @var LocalDivider
     */
    protected $divider;

    /**
     * @var bool
     */
    protected $parametrized;

    /**
     * @var NonDegradeCondition[]
     */
    protected $nonDegradeConditions;

    /**
     * VariableFraction constructor.
     * @param string $expression
     * @param LocalDivider $divider
     * @param Numerator $numerator
     */
    public function __construct(string $expression, LocalDivider $divider, Numerator $numerator)
    {
        $this->expression = $expression;
        $this->divider = $divider;
        $this->numerator = $numerator;
        $this->parametrized = false;
        $this->nonDegradeConditions = [];
    }

    /**
     * @param VariableFraction $fraction
     * @return VariableFraction
     */
    public function addFraction(VariableFraction $fraction): VariableFraction
    {
        $thisCoefficient = $this->getDivider()->getCoefficient();
        $foreignCoefficient = $fraction->getDivider()->getCoefficient();

        if($thisCoefficient >= $foreignCoefficient){
            $numeratorExpression = sprintf($this->getNumerator()->getExpression() . ' + %d (%s)', $thisCoefficient / $foreignCoefficient, $fraction->getNumerator()->getExpression());
            $this->numerator->setExpression($numeratorExpression);
            return $this;
        }

        $numeratorExpression = sprintf($fraction->getNumerator()->getExpression() . ' + %d (%s)', $foreignCoefficient / $thisCoefficient, $this->getNumerator()->getExpression());
        $fraction->getNumerator()->setExpression($numeratorExpression);
        return $fraction;
    }

    /**
     * @return bool
     */
    public function hasParameters(): bool
    {
        return Strings::match($this->numerator->getExpression(), '~p\d+~') || Strings::match($this->divider->getExpression(), '~p\d+~');
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

    /**
     * @return bool
     */
    public function isDegraded(): bool
    {
        return $this->degraded;
    }

    /**
     * @param bool $degraded
     */
    public function setDegraded(bool $degraded): void
    {
        $this->degraded = $degraded;
    }

    /**
     * @return NonDegradeCondition[]
     */
    public function getNonDegradeConditions(): array
    {
        return $this->nonDegradeConditions;
    }

    /**
     * @param NonDegradeCondition[] $nonDegradeConditions
     */
    public function setNonDegradeConditions(array $nonDegradeConditions): void
    {
        $this->nonDegradeConditions = $nonDegradeConditions;
    }

    /**
     * @return LocalDivider
     */
    public function getDivider(): LocalDivider
    {
        return $this->divider;
    }

    /**
     * @param LocalDivider $divider
     */
    public function setDivider(LocalDivider $divider): void
    {
        $this->divider = $divider;
    }

    /**
     * @return Numerator
     */
    public function getNumerator(): Numerator
    {
        return $this->numerator;
    }

    /**
     * @param Numerator $numerator
     */
    public function setNumerator(Numerator $numerator): void
    {
        $this->numerator = $numerator;
    }
}
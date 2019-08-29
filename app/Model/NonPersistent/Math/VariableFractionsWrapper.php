<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.8.19
 * Time: 16:15
 */

namespace App\Model\NonPersistent\Math;

/**
 * Class VariableFractionsWrapper
 * @package App\Model\NonPersistent\Math
 */
class VariableFractionsWrapper
{
    /**
     * @var GlobalDivider
     */
    protected $globalDivider;

    /**
     * @var VariableFraction[]
     */
    protected $varFractions;

    /**
     * @var VariableFraction[]
     */
    protected $varFractionsParametrized;

    /**
     * VariableFractionsWrapper constructor.
     * @param GlobalDivider $globalDivider
     * @param array $varFractions
     * @param array $varFractionsParametrized
     */
    public function __construct(GlobalDivider $globalDivider, array $varFractions, array $varFractionsParametrized)
    {
        $this->globalDivider = $globalDivider;
        $this->varFractions = $varFractions;
        $this->varFractionsParametrized = $varFractionsParametrized;
    }

    /**
     * @return GlobalDivider
     */
    public function getGlobalDivider(): GlobalDivider
    {
        return $this->globalDivider;
    }

    /**
     * @param GlobalDivider $globalDivider
     */
    public function setGlobalDivider(GlobalDivider $globalDivider): void
    {
        $this->globalDivider = $globalDivider;
    }

    /**
     * @return VariableFraction[]
     */
    public function getVarFractions(): array
    {
        return $this->varFractions;
    }

    /**
     * @param VariableFraction[] $varFractions
     */
    public function setVarFractions(array $varFractions): void
    {
        $this->varFractions = $varFractions;
    }

    /**
     * @return VariableFraction[]
     */
    public function getVarFractionsParametrized(): array
    {
        return $this->varFractionsParametrized;
    }

    /**
     * @param VariableFraction[] $varFractionsParametrized
     */
    public function setVarFractionsParametrized(array $varFractionsParametrized): void
    {
        $this->varFractionsParametrized = $varFractionsParametrized;
    }
}
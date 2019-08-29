<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.8.19
 * Time: 11:47
 */

namespace App\Model\NonPersistent\Entity;

use App\Model\NonPersistent\Math\GlobalDivider;
use App\Model\NonPersistent\Math\VariableFraction;
use App\Model\NonPersistent\Parameter\ParametersData;
use Nette\Utils\ArrayHash;

/**
 * Class EquationTemplateNP
 * @package App\Model\NonPersistent\Entity
 */
abstract class EquationTemplateNP extends ProblemTemplateNP
{
    /**
     * @var int
     */
    protected $rank;

    /**
     * @var GlobalDivider|null
     */
    protected $globalDivider;

    /**
     * @var bool
     */
    protected $skipZeroFractions;

    /**
     * @var ParametersData|null
     */
    protected $parametersData;

    /**
     * @var VariableFraction[]
     */
    protected $varFractions;

    /**
     * @var int|null
     */
    protected $varFractionsCnt;

    /**
     * @var VariableFraction[]
     */
    protected $varFractionsParametrized;

    /**
     * @var int
     */
    protected $varFractionsParametrizedCnt;

    /**
     * @var int
     */
    protected $fractionsToCheckCnt;

    /**
     * @var array
     */
    protected $fractionsToCheckIndexes;

    /**
     * EquationTemplateNP constructor.
     * @param ArrayHash $values
     */
    public function __construct(ArrayHash $values)
    {
        parent::__construct($values);
        $this->skipZeroFractions = false;
        $this->varFractions = [];
        $this->varFractionsCnt = 0;
        $this->varFractionsParametrized = [];
        $this->varFractionsParametrizedCnt = 0;
        $this->fractionsToCheckIndexes = [];
        $this->fractionsToCheckCnt = 0;
    }

    /**
     * @param GlobalDivider $globalDivider
     */
    public function setGlobalDivider(GlobalDivider $globalDivider): void
    {
        $this->globalDivider = $globalDivider;
    }

    /**
     * @return int
     */
    public function calculateFractionsToCheckCnt(): ?int
    {
        if($this->getVarFractionsCnt() === null){
            return null;
        }
        return ($this->rank - ($this->getVarFractionsCnt() - $this->getVarFractionsParametrizedCnt())) ?: 0;
    }

    /**
     * @return GlobalDivider
     */
    public function getGlobalDivider(): GlobalDivider
    {
        return $this->globalDivider;
    }

    /**
     * @return bool
     */
    public function isSkipZeroFractions(): bool
    {
        return $this->skipZeroFractions;
    }

    /**
     * @param bool $skipZeroFractions
     */
    public function setSkipZeroFractions(bool $skipZeroFractions): void
    {
        $this->skipZeroFractions = $skipZeroFractions;
    }

    /**
     * @return ParametersData|null
     */
    public function getParametersData(): ?ParametersData
    {
        return $this->parametersData;
    }

    /**
     * @param ParametersData|null $parametersData
     */
    public function setParametersData(?ParametersData $parametersData): void
    {
        $this->parametersData = $parametersData;
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
        $this->varFractionsCnt = count($varFractions);
    }

    /**
     * @return int|null
     */
    public function getVarFractionsCnt(): ?int
    {
        return $this->varFractionsCnt;
    }

    /**
     * @return VariableFraction[]
     */
    public function getVarFractionsParametrized(): array
    {
        return $this->varFractionsParametrized;
    }

    /**
     * @param array $varFractionsParametrized
     */
    public function setVarFractionsParametrized(array $varFractionsParametrized): void
    {
        $this->varFractionsParametrized = $varFractionsParametrized;
        $this->varFractionsParametrizedCnt = count($varFractionsParametrized);
    }

    /**
     * @return int
     */
    public function getVarFractionsParametrizedCnt(): int
    {
        return $this->varFractionsParametrizedCnt;
    }

    /**
     * @return int
     */
    public function getFractionsToCheckCnt(): int
    {
        return $this->fractionsToCheckCnt;
    }

    /**
     * @param int $fractionsToCheckCnt
     */
    public function setFractionsToCheckCnt(int $fractionsToCheckCnt): void
    {
        $this->fractionsToCheckCnt = $fractionsToCheckCnt;
    }

    /**
     * @return array
     */
    public function getFractionsToCheckIndexes(): array
    {
        return $this->fractionsToCheckIndexes;
    }

    /**
     * @param array $fractionsToCheckIndexes
     */
    public function setFractionsToCheckIndexes(array $fractionsToCheckIndexes): void
    {
        $this->fractionsToCheckIndexes = $fractionsToCheckIndexes;
    }
}
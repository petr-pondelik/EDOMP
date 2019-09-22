<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.8.19
 * Time: 11:47
 */

namespace App\Model\NonPersistent\Entity;

use App\Model\NonPersistent\Math\GlobalDivider;
use App\Model\NonPersistent\Math\NonDegradeCondition;
use App\Model\NonPersistent\Math\VariableFraction;
use App\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use Nette\Utils\ArrayHash;

/**
 * Class EquationTemplateNP
 * @package App\Model\NonPersistent\Entity
 */
abstract class EquationTemplateNP extends ProblemTemplateNP
{
    /**
     * @var string|null
     */
    protected $variable;

    /**
     * @var GlobalDivider|null
     */
    protected $globalDivider;

    /**
     * @var array
     */
    protected $variableFractionsData;

    /**
     * @var VariableFraction[]
     */
    protected $varFractionsStatic;

    /**
     * @var VariableFraction[]
     */
    protected $varFractionsParametrized;

    /**
     * @var NonDegradeCondition[]
     */
    protected $nonDegradeConditions;

    /**
     * EquationTemplateNP constructor.
     * @param ArrayHash $values
     * @param ProblemTemplate|null $original
     */
    public function __construct(ArrayHash $values, ProblemTemplate $original = null)
    {
        bdump('EQUATION CONSTRUCTOR');
        parent::__construct($values, $original);
        $this->variableFractionsData = [];
        $this->varFractionsStatic = [];
        $this->varFractionsParametrized = [];
        $this->nonDegradeConditions = [];
    }

    /**
     * @param GlobalDivider $globalDivider
     */
    public function setGlobalDivider(GlobalDivider $globalDivider): void
    {
        $this->globalDivider = $globalDivider;
    }

    /**
     * @return GlobalDivider
     */
    public function getGlobalDivider(): GlobalDivider
    {
        return $this->globalDivider;
    }

    /**
     * @return VariableFraction[]
     */
    public function getVarFractions(): array
    {
        return array_merge($this->getVarFractionsStatic(), $this->getVarFractionsParametrized());
    }

    /**
     * @return VariableFraction[]
     */
    public function getVarFractionsStatic(): array
    {
        return $this->varFractionsStatic;
    }

    /**
     * @param array $varFractionsStatic
     */
    public function setVarFractionsStatic(array $varFractionsStatic): void
    {
        $this->varFractionsStatic = $varFractionsStatic;
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
    }

    /**
     * @return array
     */
    public function getVariableFractionsData(): array
    {
        return $this->variableFractionsData;
    }

    /**
     * @param array $variableFractionsData
     */
    public function setVariableFractionsData(array $variableFractionsData): void
    {
        $this->variableFractionsData = $variableFractionsData;
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
     * @return string|null
     */
    public function getVariable(): ?string
    {
        return $this->variable;
    }

    /**
     * @param string|null $variable
     */
    public function setVariable(?string $variable): void
    {
        $this->variable = $variable;
    }
}
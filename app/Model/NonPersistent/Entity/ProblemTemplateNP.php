<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.8.19
 * Time: 19:20
 */

namespace App\Model\NonPersistent\Entity;

use App\Model\NonPersistent\Math\GlobalDivider;
use App\Model\NonPersistent\Parameter\ParametersData;
use App\Model\NonPersistent\Traits\SetValuesTrait;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemTemplate
 * @package App\Model\NonPersistent\Entity\Entity
 */
abstract class ProblemTemplateNP extends BaseEntityNP
{
    use SetValuesTrait;

    /**
     * @var int|null
     */
    protected $idHidden;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var int|null
     */
    protected $subCategory;

    /**
     * @var string|null
     */
    protected $body;

    /**
     * @var string|null
     */
    protected $textBefore;

    /**
     * @var string|null
     */
    protected $textAfter;

    /**
     * @var string|null
     */
    protected $variable;

    /**
     * @var int|null
     */
    protected $difficulty;

    /**
     * @var string|null
     */
    protected $expression;

    /**
     * @var string|null
     */
    protected $standardized;

    /**
     * @var int|null
     */
    protected $conditionType;

    /**
     * @var int|null
     */
    protected $conditionAccessor;

    /**
     * @var string
     */
    protected $conditionValidateItem;

    /**
     * @var GlobalDivider
     */
    protected $globalDivider;

    /**
     * @var bool
     */
    protected $skipZeroFractions;

    /**
     * @var ParametersData
     */
    protected $parametersData;

    /**
     * LinearEquationTemplate constructor.
     * @param ArrayHash $values
     */
    public function __construct(ArrayHash $values)
    {
        $this->setValues($values);
        $this->conditionValidateItem = 'standardized';
        $this->skipZeroFractions = false;
    }

    /**
     * @return mixed
     */
    public function getConditionValidateData()
    {
        return $this->{$this->conditionValidateItem};
    }

    /**
     * @param GlobalDivider $globalDivider
     */
    public function setGlobalDivider(GlobalDivider $globalDivider): void
    {
        $this->globalDivider = $globalDivider;
        if($globalDivider->getFactors()){
            $this->skipZeroFractions = true;
        }
    }

    /**
     * @return string
     */
    public function getConditionValidateItem(): string
    {
        return $this->conditionValidateItem;
    }

    /**
     * @param string $conditionValidateItem
     */
    public function setConditionValidateItem(string $conditionValidateItem): void
    {
        $this->conditionValidateItem = $conditionValidateItem;
    }

    /**
     * @return int|null
     */
    public function getIdHidden(): ?int
    {
        return $this->idHidden;
    }

    /**
     * @param int|null $idHidden
     */
    public function setIdHidden(?int $idHidden): void
    {
        $this->idHidden = $idHidden;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return int|null
     */
    public function getSubCategory(): ?int
    {
        return $this->subCategory;
    }

    /**
     * @param int|null $subCategory
     */
    public function setSubCategory(?int $subCategory): void
    {
        $this->subCategory = $subCategory;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string|null $body
     */
    public function setBody(?string $body): void
    {
        $this->body = $body;
    }

    /**
     * @return string|null
     */
    public function getTextBefore(): ?string
    {
        return $this->textBefore;
    }

    /**
     * @param string|null $textBefore
     */
    public function setTextBefore(?string $textBefore): void
    {
        $this->textBefore = $textBefore;
    }

    /**
     * @return string|null
     */
    public function getTextAfter(): ?string
    {
        return $this->textAfter;
    }

    /**
     * @param string|null $textAfter
     */
    public function setTextAfter(?string $textAfter): void
    {
        $this->textAfter = $textAfter;
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

    /**
     * @return int|null
     */
    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }

    /**
     * @param int|null $difficulty
     */
    public function setDifficulty(?int $difficulty): void
    {
        $this->difficulty = $difficulty;
    }

    /**
     * @return string|null
     */
    public function getExpression(): ?string
    {
        return $this->expression;
    }

    /**
     * @param string|null $expression
     */
    public function setExpression(?string $expression): void
    {
        $this->expression = $expression;
    }

    /**
     * @return string|null
     */
    public function getStandardized(): ?string
    {
        return $this->standardized;
    }

    /**
     * @param string|null $standardized
     */
    public function setStandardized(?string $standardized): void
    {
        $this->standardized = $standardized;
    }

    /**
     * @return int|null
     */
    public function getConditionType(): ?int
    {
        return $this->conditionType;
    }

    /**
     * @param int|null $conditionType
     */
    public function setConditionType(?int $conditionType): void
    {
        $this->conditionType = $conditionType;
    }

    /**
     * @return int|null
     */
    public function getConditionAccessor(): ?int
    {
        return $this->conditionAccessor;
    }

    /**
     * @param int|null $conditionAccessor
     */
    public function setConditionAccessor(?int $conditionAccessor): void
    {
        $this->conditionAccessor = $conditionAccessor;
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
     * @return ParametersData
     */
    public function getParametersData(): ParametersData
    {
        return $this->parametersData;
    }

    /**
     * @param ParametersData $parametersData
     */
    public function setParametersData(ParametersData $parametersData): void
    {
        $this->parametersData = $parametersData;
    }
}
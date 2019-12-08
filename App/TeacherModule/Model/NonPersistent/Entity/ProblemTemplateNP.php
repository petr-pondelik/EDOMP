<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.8.19
 * Time: 19:20
 */

namespace App\TeacherModule\Model\NonPersistent\Entity;

use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use App\TeacherModule\Model\NonPersistent\TemplateData\ParametersData;
use App\TeacherModule\Model\NonPersistent\TemplateData\ProblemTemplateState;
use App\TeacherModule\Model\NonPersistent\TemplateData\ProblemTemplateStateItem;
use App\TeacherModule\Model\NonPersistent\Traits\SetValuesTrait;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class ProblemTemplateNP
 * @package App\TeacherModule\Model\NonPersistent\Entity
 */
abstract class ProblemTemplateNP extends BaseEntityNP
{
    use SetValuesTrait;

    /**
     * @var int|null
     */
    protected $id;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var int|null
     */
    protected $subTheme;

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
     * @var ParametersData|null
     */
    protected $parametersData;

    /**
     * @var ProblemTemplateState
     */
    protected $state;

    /**
     * ProblemTemplateNP constructor.
     * @param iterable $values
     * @param ProblemTemplate|null $original
     */
    public function __construct(iterable $values, ProblemTemplate $original = null)
    {
        bdump('PROBLEM TEMPLATE NP CONSTRUCTOR');
        $this->conditionValidateItem = 'standardized';
        $this->setValues($values);
        $this->state = new ProblemTemplateState();

        // Initialize ProblemTemplate state based on action (create or update)
        if ($original) {
            $this->id = $original->getId();
            $this->state->update(new ProblemTemplateStateItem('type', true, true));
            $originalConditions = $original->getConditions()->getValues();
            foreach ($originalConditions as $originalCondition) {
                $problemConditionTypeId = $originalCondition->getProblemConditionType()->getId();
                $rule = 'condition_' . $problemConditionTypeId;
                $originalValue = $originalCondition->getAccessor();
                $newValue = $values[$rule] ?? $values['conditionAccessor'];
                if ((int)$originalValue !== (int)$newValue) {
                    $this->state->update(new ProblemTemplateStateItem($rule, $newValue, false));
                } else {
                    $this->state->update(new ProblemTemplateStateItem($rule, $newValue, true));
                }
            }
        } else {
            $this->state->update(new ProblemTemplateStateItem('type', false, false));
            foreach ($values as $key => $value) {
                if (Strings::match($key, '~condition_\d~')) {
                    if ($value === 0) {
                        $this->state->update(new ProblemTemplateStateItem($key, $value, true));
                    } else {
                        $this->state->update(new ProblemTemplateStateItem($key, $value, false));
                    }
                }
            }
        }

    }

    /**
     * @return mixed
     */
    public function getConditionValidateData()
    {
        return $this->{$this->conditionValidateItem};
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
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
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
    public function getSubTheme(): ?int
    {
        return $this->subTheme;
    }

    /**
     * @param int|null $subTheme
     */
    public function setSubTheme(?int $subTheme): void
    {
        $this->subTheme = $subTheme;
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
     * @return ProblemTemplateState
     */
    public function getState(): ProblemTemplateState
    {
        return $this->state;
    }

    /**
     * @param ProblemTemplateState $state
     */
    public function setState(ProblemTemplateState $state): void
    {
        $this->state = $state;
    }
}
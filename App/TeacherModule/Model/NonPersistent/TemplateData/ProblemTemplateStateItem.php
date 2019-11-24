<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.9.19
 * Time: 21:18
 */

namespace App\TeacherModule\Model\NonPersistent\TemplateData;

/**
 * Class ProblemTemplateStateItem
 * @package App\TeacherModule\Model\NonPersistent\Entity
 */
class ProblemTemplateStateItem
{
    /**
     * @var string
     */
    protected $rule;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var bool
     */
    protected $validated;

    /**
     * ProblemTemplateStateItem constructor.
     * @param string $rule
     * @param string $value
     * @param bool $validated
     */
    public function __construct(string $rule, string $value, bool $validated)
    {
        $this->rule = $rule;
        $this->value = $value;
        $this->validated = $validated;
    }

    /**
     * @return string
     */
    public function getRule(): string
    {
        return $this->rule;
    }

    /**
     * @param string $rule
     */
    public function setRule(string $rule): void
    {
        $this->rule = $rule;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function isValidated(): bool
    {
        return $this->validated;
    }

    /**
     * @param bool $validated
     */
    public function setValidated(bool $validated): void
    {
        $this->validated = $validated;
    }
}
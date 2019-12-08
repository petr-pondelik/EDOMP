<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 3.9.19
 * Time: 22:37
 */

namespace App\TeacherModule\Model\NonPersistent\Entity;

/**
 * Class SequenceTemplateNP
 * @package App\TeacherModule\Model\NonPersistent\Entity
 */
class SequenceTemplateNP extends ProblemTemplateNP
{
    /**
     * @var string|null
     */
    protected $indexVariable;

    /**
     * @var int|null
     */
    protected $firstN;

    /**
     * @var array|null
     */
    protected $firstValues;

    /**
     * SequenceTemplateNP constructor.
     * @param iterable $values
     */
    public function __construct(iterable $values)
    {
        $this->firstValues = [];
        parent::__construct($values);
    }

    /**
     * @return int|null
     */
    public function getFirstN(): ?int
    {
        return $this->firstN;
    }

    /**
     * @param int|null $firstN
     */
    public function setFirstN(?int $firstN): void
    {
        $this->firstN = $firstN;
    }

    /**
     * @return array|null
     */
    public function getFirstValues(): ?array
    {
        return $this->firstValues;
    }

    /**
     * @param array|null $firstValues
     */
    public function setFirstValues(?array $firstValues): void
    {
        $this->firstValues = $firstValues;
    }

    /**
     * @return string|null
     */
    public function getIndexVariable(): ?string
    {
        return $this->indexVariable;
    }

    /**
     * @param string|null $indexVariable
     */
    public function setIndexVariable(?string $indexVariable): void
    {
        $this->indexVariable = $indexVariable;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 3.9.19
 * Time: 22:37
 */

namespace App\Model\NonPersistent\Entity;

use Nette\Utils\ArrayHash;

/**
 * Class SequenceTemplateNP
 * @package App\Model\NonPersistent\Entity
 */
class SequenceTemplateNP extends ProblemTemplateNP
{
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
     * @param ArrayHash $values
     */
    public function __construct(ArrayHash $values)
    {
        parent::__construct($values);
        $this->firstValues = [];
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
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 18:50
 */

namespace App\Model\Entity;

use App\Model\Traits\LabelTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\ProblemTypeRepository")
 *
 * Class ProblemType
 * @package App\Model\Entity
 */
class ProblemType extends BaseEntity
{
    use LabelTrait;

    /**
     * @var string
     */
    protected $toStringAttr = "label";

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(
     *     type="bool",
     *     message="IsGeneratable must be {{ type }}."
     * )
     *
     * @var bool
     */
    protected $isGeneratable;

    /**
     * @ORM\OneToMany(targetEntity="ProblemFinal", mappedBy="problemType", cascade={"persist", "merge"})
     *
     * @var ArrayCollection|ProblemFinal[]
     */
    protected $problems;

    /**
     * @ORM\ManyToMany(targetEntity="App\Model\Entity\ProblemConditionType", inversedBy="problemTypes", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="problem_tp_problem_condition_tp_rel")
     */
    protected $conditionTypes;

    /**
     * ProblemType constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->isGeneratable = false;
        $this->problems = new ArrayCollection();
        $this->conditionTypes = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getProblems(): ?Collection
    {
        return $this->problems;
    }

    /**
     * @param Collection $problems
     */
    public function setProblems($problems): void
    {
        $this->problems = $problems;
    }

    /**
     * @return Collection/ProblemConditionType[]
     */
    public function getConditionTypes(): ?Collection
    {
        return $this->conditionTypes;
    }

    /**
     * @param mixed $conditionTypes
     */
    public function setConditionTypes($conditionTypes): void
    {
        $this->conditionTypes = $conditionTypes;
    }

    /**
     * @return bool
     */
    public function isGeneratable(): bool
    {
        return $this->isGeneratable;
    }

    /**
     * @param bool $isGeneratable
     */
    public function setIsGeneratable(bool $isGeneratable): void
    {
        $this->isGeneratable = $isGeneratable;
    }
}
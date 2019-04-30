<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 18:50
 */

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\DateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\ProblemTypeRepository")
 *
 * Class ProblemType
 * @package App\Model\Entity
 */
class ProblemType
{
    //Identifier trait for id column
    use Identifier;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $label;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\NotBlank()
     *
     * @var int
     */
    protected $accessor;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\NotBlank()
     *
     * @var bool
     */
    protected $isGeneratable;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @var DateTime
     */
    protected $created;

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
        $this->isGeneratable = false;
        $this->created = new DateTime();
        $this->problems = new ArrayCollection();
        $this->conditionTypes = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     */
    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
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
     * @return string
     */
    public function __toString()
    {
        return $this->label;
    }
}
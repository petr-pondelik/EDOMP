<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 22:45
 */

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\DateTime;
use Symfony\Component\Validator\Constraints as Assert;

use App\Model\Traits\ToStringTrait;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\ProblemRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *     "problemfinal" = "ProblemFinal",
 *     "problemtemplate" = "ProblemTemplate",
 *     "lineareq" = "LinearEqTempl",
 *     "quadraticeq" = "QuadraticEqTempl",
 *     "arithmeticseq" = "ArithmeticSeqTempl",
 *     "geometicSeq" = "GeometricSeqTempl"
 * })
 *
 * Class ProblemFinal
 * @package App\Model\Entity
 */
abstract class Problem
{
    //Identifier trait for id column
    use Identifier;

    use ToStringTrait;

    /**
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $body;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $textBefore;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $textAfter;

    /**
     * @ORM\Column(type="float", nullable=true)
     *
     * @var float
     */
    protected $successRate;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     *
     * @var bool
     */
    protected $isTemplate;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @Assert\NotBlank()
     *
     * @var DateTime
     */
    protected $created;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\ProblemType", cascade={"persist", "merge"})
     *
     * @var ProblemType
     */
    protected $problemType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\Difficulty", cascade={"persist", "merge"})
     * @Assert\NotBlank()
     *
     * @var Difficulty
     */
    protected $difficulty;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\SubCategory", cascade={"persist", "merge"})
     * @Assert\NotBlank()
     *
     * @var SubCategory
     */
    protected $subCategory;

    /**
     * @ORM\ManyToMany(targetEntity="ProblemCondition", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="problem_condition_problem_template_rel")
     */
    protected $conditions;

    /**
     * @ORM\OneToMany(targetEntity="App\Model\Entity\ProblemTestAssociation", mappedBy="problemTemplate", cascade={"all"})
     */
    protected $testAssociations;

    /**
     * ProblemFinal constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->created = new DateTime();
        $this->conditions = new ArrayCollection();
        $this->testAssociations = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getTextBefore(): string
    {
        return $this->textBefore;
    }

    /**
     * @param string $textBefore
     */
    public function setTextBefore(string $textBefore): void
    {
        $this->textBefore = $textBefore;
    }

    /**
     * @return string
     */
    public function getTextAfter(): string
    {
        return $this->textAfter;
    }

    /**
     * @param string $textAfter
     */
    public function setTextAfter(string $textAfter): void
    {
        $this->textAfter = $textAfter;
    }

    /**
     * @return float
     */
    public function getSuccessRate(): ?float
    {
        return $this->successRate;
    }

    /**
     * @param float $successRate
     */
    public function setSuccessRate(float $successRate): void
    {
        $this->successRate = $successRate;
    }

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return DateTime::from($this->created);
    }

    /**
     * @param DateTime $created
     */
    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
    }

    /**
     * @return ProblemType
     */
    public function getProblemType(): ProblemType
    {
        return $this->problemType;
    }

    /**
     * @param ProblemType $problemType
     */
    public function setProblemType(ProblemType $problemType): void
    {
        $this->problemType = $problemType;
    }

    /**
     * @return Difficulty
     */
    public function getDifficulty(): Difficulty
    {
        return $this->difficulty;
    }

    /**
     * @param Difficulty $difficulty
     */
    public function setDifficulty(Difficulty $difficulty): void
    {
        $this->difficulty = $difficulty;
    }

    /**
     * @return SubCategory
     */
    public function getSubCategory(): SubCategory
    {
        return $this->subCategory;
    }

    /**
     * @param SubCategory $subCategory
     */
    public function setSubCategory(SubCategory $subCategory): void
    {
        $this->subCategory = $subCategory;
    }

    /**
     * @return mixed
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param mixed $conditions
     */
    public function setConditions($conditions): void
    {
        $this->conditions = $conditions;
    }

    /**
     * @param ProblemCondition $condition
     */
    public function addCondition(ProblemCondition $condition): void
    {
        if($this->conditions->contains($condition)) return;
        $this->conditions[] = $condition;
    }

    /**
     * @return mixed
     */
    public function getTestAssociations()
    {
        return $this->testAssociations;
    }

    /**
     * @param mixed $testAssociations
     */
    public function setTestAssociations($testAssociations): void
    {
        $this->testAssociations = $testAssociations;
    }
}
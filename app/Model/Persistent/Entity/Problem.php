<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 22:45
 */

namespace App\Model\Persistent\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Persistent\Repository\ProblemRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *     "problemfinal" = "App\Model\Persistent\Entity\ProblemFinal\ProblemFinal",
 *     "problemTemplate" = "App\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate",
 *     "linearequationfinal" = "App\Model\Persistent\Entity\ProblemFinal\LinearEquationFinal",
 *     "quadraticequationfinal" = "App\Model\Persistent\Entity\ProblemFinal\QuadraticEquationFinal",
 *     "arithmeticsequencefinal" = "App\Model\Persistent\Entity\ProblemFinal\ArithmeticSequenceFinal",
 *     "geometricsequencefinal" = "App\Model\Persistent\Entity\ProblemFinal\GeometricSequenceFinal",
 *     "lineareqtemplate" = "App\Model\Persistent\Entity\ProblemTemplate\LinearEquationTemplate",
 *     "quadraticeqtemplate" = "App\Model\Persistent\Entity\ProblemTemplate\QuadraticEquationTemplate",
 *     "arithmeticseqtemplate" = "App\Model\Persistent\Entity\ProblemTemplate\ArithmeticSequenceTemplate",
 *     "geometicseqtemplate" = "App\Model\Persistent\Entity\ProblemTemplate\GeometricSequenceTemplate"
 * })
 *
 * Class ProblemFinal
 * @package App\Model\Persistent\Entity
 */
abstract class Problem extends BaseEntity
{
    /**
     * @var string
     */
    protected $toStringAttr = 'body';

    /**
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank(
     *     message="Body can't be blank."
     * )
     * @Assert\Type(
     *     type="string",
     *     message="Body must be {{ type }}."
     * )
     *
     * @var string
     */
    protected $body;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Type(
     *     type="string",
     *     message="TextBefore must be {{ type }}."
     * )
     *
     * @var string
     */
    protected $textBefore;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Type(
     *     type="string",
     *     message="TextAfter must be {{ type }}."
     * )
     * @var string
     */
    protected $textAfter;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Type(
     *     type="float",
     *     message="SuccessRate must be {{ type }}."
     * )
     *
     * @var float
     */
    protected $successRate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(
     *     type="bool",
     *     message="IsTemplate must be {{ type }}."
     * )
     *
     * @var bool
     */
    protected $isTemplate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Persistent\Entity\ProblemType", cascade={"persist", "merge"})
     *
     * @var ProblemType|null
     */
    protected $problemType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Persistent\Entity\Difficulty", cascade={"persist", "merge"})
     * @Assert\NotBlank(
     *     message="Difficulty can't be blank."
     * )
     *
     * @var Difficulty
     */
    protected $difficulty;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Persistent\Entity\SubCategory", cascade={"persist", "merge"})
     * @Assert\NotBlank(
     *     message="SubCategory can't be blank."
     * )
     *
     * @var SubCategory
     */
    protected $subCategory;

    /**
     * @ORM\ManyToMany(targetEntity="ProblemCondition", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="problem_condition_problem_rel")
     *
     * @var ProblemCondition[]
     */
    protected $conditions;

    /**
     * Problem constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->conditions = new ArrayCollection();
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
    public function getTextBefore(): ?string
    {
        return $this->textBefore;
    }

    /**
     * @param string|null $textBefore
     */
    public function setTextBefore(string $textBefore = null): void
    {
        $this->textBefore = $textBefore;
    }

    /**
     * @return string
     */
    public function getTextAfter(): ?string
    {
        return $this->textAfter;
    }

    /**
     * @param string|null $textAfter
     */
    public function setTextAfter(string $textAfter = null): void
    {
        $this->textAfter = $textAfter;
    }

    /**
     * @return float
     */
    public function getSuccessRate(): ?float
    {
        if(empty($this->successRate))
            return null;
        return round($this->successRate, 2);
    }

    /**
     * @param float|null $successRate
     */
    public function setSuccessRate(float $successRate = null): void
    {
        $this->successRate = $successRate;
    }

    /**
     * @return ProblemType|null
     */
    public function getProblemType(): ?ProblemType
    {
        return $this->problemType;
    }

    /**
     * @param ProblemType|null $problemType
     */
    public function setProblemType(?ProblemType $problemType): void
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
        if($this->conditions->contains($condition)){
            return;
        }
        $this->conditions[] = $condition;
    }

    /**
     * @return bool
     */
    public function isTemplate(): bool
    {
        return $this->isTemplate;
    }

    /**
     * @param bool $isTemplate
     */
    public function setIsTemplate(bool $isTemplate): void
    {
        $this->isTemplate = $isTemplate;
    }
}
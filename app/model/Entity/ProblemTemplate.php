<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:17
 */

namespace App\Model\Entity;

use App\Model\Traits\LabelTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\DateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\ProblemTemplateRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *     "problem" = "ProblemTemplate",
 *     "lineareq" = "LinearEqTempl",
 *     "quadraticeq" = "QuadraticEqTempl",
 *     "arithmeticseq" = "ArithmeticSeqTempl",
 *     "geometicSeq" = "GeometricSeqTempl"
 * })
 *
 * Class ProblemTemplate
 * @package App\Model\Entity
 */
class ProblemTemplate
{
    //Identifier trait for id column
    use Identifier;

    use LabelTrait;

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
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $result;

    /**
     * @ORM\Column(type="float", nullable=true)
     *
     * @var float
     */
    protected $successRate;

    /**
     * @ORM\Column(type="json", nullable=true)
     *
     */
    protected $matches;

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
    public function getTextBefore(): ?string
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
    public function getTextAfter(): ?string
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
     * @return string
     */
    public function getResult(): string
    {
        return $this->result;
    }

    /**
     * @param string $result
     */
    public function setResult(string $result): void
    {
        $this->result = $result;
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
     * @return mixed
     */
    public function getMatches()
    {
        return $this->matches;
    }

    /**
     * @param mixed $matches
     */
    public function setMatches($matches): void
    {
        $this->matches = $matches;
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
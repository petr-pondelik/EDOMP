<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 22:45
 */

namespace App\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Traits\CreatedByTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\CoreModule\Model\Persistent\Repository\ProblemRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *     "problemfinal" = "App\CoreModule\Model\Persistent\Entity\ProblemFinal",
 *     "problemTemplate" = "App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate",
 *     "lineareqtemplate" = "App\CoreModule\Model\Persistent\Entity\ProblemTemplate\LinearEquationTemplate",
 *     "quadraticeqtemplate" = "App\CoreModule\Model\Persistent\Entity\ProblemTemplate\QuadraticEquationTemplate",
 *     "arithmeticseqtemplate" = "App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ArithmeticSequenceTemplate",
 *     "geometicseqtemplate" = "App\CoreModule\Model\Persistent\Entity\ProblemTemplate\GeometricSequenceTemplate"
 * })
 *
 * Class ProblemFinal
 * @package App\CoreModule\Model\Persistent\Entity
 */
abstract class Problem extends BaseEntity
{
    use CreatedByTrait;

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
     * @ORM\ManyToOne(targetEntity="App\CoreModule\Model\Persistent\Entity\ProblemType", cascade={"persist", "merge"})
     *
     * @var ProblemType|null
     */
    protected $problemType;

    /**
     * @ORM\ManyToOne(targetEntity="App\CoreModule\Model\Persistent\Entity\Difficulty", cascade={"persist", "merge"})
     * @Assert\NotBlank(
     *     message="Difficulty can't be blank."
     * )
     *
     * @var Difficulty
     */
    protected $difficulty;

    /**
     * @ORM\ManyToOne(targetEntity="App\CoreModule\Model\Persistent\Entity\SubTheme", cascade={"persist", "merge"})
     * @Assert\NotBlank(
     *     message="SubTheme can't be blank."
     * )
     *
     * @var SubTheme
     */
    protected $subTheme;

    /**
     * @ORM\ManyToMany(targetEntity="ProblemCondition", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="problem_condition_problem_rel")
     *
     * @var PersistentCollection|ArrayCollection
     */
    protected $conditions;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(
     *     type="bool",
     *     message="IsGeneratable must be {{ type }}."
     * )
     *
     * @var bool
     */
    protected $isGenerated = false;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(
     *     type="bool",
     *     message="StudentVisible must be {{ type }}."
     * )
     *
     * @var boolean
     */
    protected $studentVisible;

    /**
     * Problem constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->teacherLevelSecured = true;
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
     * @return float|null
     */
    public function getSuccessRate(): ?float
    {
        if(empty($this->successRate)) {
            return null;
        }
        return round($this->successRate, 2);
    }

    /**
     * @param float|null $successRate
     */
    public function setSuccessRate(?float $successRate): void
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
     * @return SubTheme
     */
    public function getSubTheme(): SubTheme
    {
        return $this->subTheme;
    }

    /**
     * @param SubTheme $subTheme
     */
    public function setSubTheme(SubTheme $subTheme): void
    {
        $this->subTheme = $subTheme;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param $conditions
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

    /**
     * @return bool
     */
    public function isGenerated(): bool
    {
        return $this->isGenerated;
    }

    /**
     * @param bool $isGenerated
     */
    public function setIsGenerated(bool $isGenerated): void
    {
        $this->isGenerated = $isGenerated;
    }

    /**
     * @return bool
     */
    public function isStudentVisible(): bool
    {
        return $this->studentVisible;
    }

    /**
     * @param bool $studentVisible
     */
    public function setStudentVisible(bool $studentVisible): void
    {
        $this->studentVisible = $studentVisible;
    }
}
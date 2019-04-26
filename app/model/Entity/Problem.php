<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 12:29
 */

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\DateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\ProblemRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *     "problem" = "Problem",
 *     "lineareq" = "LinearEq"
 * })
 *
 * Class Problem
 * @package App\Model\Entity
 */
class Problem
{
    //Identifier trait for id column
    use Identifier;

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
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\NotBlank()
     *
     * @var bool
     */
    protected $isGenerated = false;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @Assert\NotBlank()
     *
     * @var DateTime
     */
    protected $created;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\ProblemType", inversedBy="problems", cascade={"persist"})
     *
     * @var ProblemType
     */
    protected $problemType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\Difficulty", inversedBy="problems", cascade={"persist"})
     * @Assert\NotBlank()
     *
     * @var Difficulty
     */
    protected $difficulty;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\SubCategory", inversedBy="problems", cascade={"persist"})
     * @Assert\NotBlank()
     *
     * @var SubCategory
     */
    protected $subCategory;

    /**
     * Problem constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->created = new DateTime();
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

}
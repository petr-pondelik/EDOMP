<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 10:36
 */

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\DateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\ProblemTestAssociationRepository")
 *
 * Class ProblemTestAssociation
 * @package App\Model\Entity
 */
class ProblemTestAssociation
{
    use Identifier;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $variant;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\NotBlank()
     *
     * @var bool
     */
    protected $nextPage;

    /**
     * @ORM\Column(type="float", nullable=true)
     *
     * @var float
     */
    protected $successRate;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @var DateTime
     */
    protected $created;

    /**
     * @ORM\ManyToOne(targetEntity="ProblemFinal", inversedBy="testAssociations", cascade={"persist", "merge"})
     * @Assert\NotBlank()
     *
     * @var ProblemFinal
     */
    protected $problem;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\ProblemTemplate", cascade={"persist", "merge"})
     *
     * @var ProblemTemplate
     */
    protected $problemTemplate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\Test", inversedBy="problemAssociations", cascade={"persist", "merge"})
     *
     * @var Test
     */
    protected $test;

    /**
     * ProblemTestAssociation constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->created = new DateTime();
    }

    /**
     * @return string
     */
    public function getVariant(): string
    {
        return $this->variant;
    }

    /**
     * @param string $variant
     */
    public function setVariant(string $variant): void
    {
        $this->variant = $variant;
    }

    /**
     * @return bool
     */
    public function isNextPage(): bool
    {
        return $this->nextPage;
    }

    /**
     * @param bool $nextPage
     */
    public function setNextPage(bool $nextPage): void
    {
        $this->nextPage = $nextPage;
    }

    /**
     * @return float
     */
    public function getSuccessRate(): float
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
     * @return ProblemFinal
     */
    public function getProblem(): ProblemFinal
    {
        return $this->problem;
    }

    /**
     * @param ProblemFinal $problem
     */
    public function setProblem(ProblemFinal $problem): void
    {
        $this->problem = $problem;
    }

    /**
     * @return ProblemTemplate
     */
    public function getProblemTemplate(): ProblemTemplate
    {
        return $this->problemTemplate;
    }

    /**
     * @param ProblemTemplate $problemTemplate
     */
    public function setProblemTemplate(ProblemTemplate $problemTemplate): void
    {
        $this->problemTemplate = $problemTemplate;
    }

    /**
     * @return Test
     */
    public function getTest(): Test
    {
        return $this->test;
    }

    /**
     * @param Test $test
     */
    public function setTest(Test $test): void
    {
        $this->test = $test;
    }
}
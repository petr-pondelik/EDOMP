<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 12:29
 */

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\ProblemFinalRepository")
 *
 * Class ProblemFinal
 * @package App\Model\Entity
 */
class ProblemFinal extends Problem
{
    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Type(
     *     type="string",
     *     message="Result must be {{ type }}."
     * )
     *
     * @var string
     */
    protected $result;

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
     * @ORM\Column(type="string", nullable=true, length=1)
     * @Assert\Type(
     *     type="string",
     *     message="Variable must be {{ type }}."
     * )
     * @Assert\Length(
     *     min=1,
     *     max=1,
     *     exactMessage="Variable must be string of length 1.",
     * )
     *
     * @var string
     */
    protected $variable;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(
     *     type="int",
     *     message="FirstN must be {{ type }}."
     * )
     *
     * @var int
     */
    protected $firstN;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\ProblemTemplate", cascade={"persist", "merge"})
     *
     * @var ProblemTemplate
     */
    protected $problemTemplate;

    /**
     * @ORM\OneToMany(targetEntity="ProblemFinalTestVariantAssociation", mappedBy="problemFinal", cascade={"all"})
     *
     * @var ProblemFinalTestVariantAssociation[]
     */
    protected $testVariantAssociations;

    /**
     * ProblemFinal constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->isTemplate = false;
        $this->testVariantAssociations = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getResult(): ?string
    {
        return $this->result;
    }

    /**
     * @param string|null $result
     */
    public function setResult(string $result = null): void
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
     * @return ProblemTemplate|null
     */
    public function getProblemTemplate(): ?ProblemTemplate
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
     * @return string|null
     */
    public function getVariable(): ?string
    {
        return $this->variable;
    }

    /**
     * @param string|null $variable
     */
    public function setVariable(string $variable = null): void
    {
        $this->variable = $variable;
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
    public function setFirstN(int $firstN = null): void
    {
        $this->firstN = $firstN;
    }

    /**
     * @return ProblemFinalTestVariantAssociation[]
     */
    public function getTestVariantAssociations(): array
    {
        return $this->testVariantAssociations;
    }

    /**
     * @param ProblemFinalTestVariantAssociation[] $testVariantAssociations
     */
    public function setTestVariantAssociations(array $testVariantAssociations): void
    {
        $this->testVariantAssociations = $testVariantAssociations;
    }

}
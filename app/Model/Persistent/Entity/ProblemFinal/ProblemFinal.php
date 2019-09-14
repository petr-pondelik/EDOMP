<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 12:29
 */

namespace App\Model\Persistent\Entity\ProblemFinal;

use App\Model\Persistent\Entity\Problem;
use App\Model\Persistent\Entity\ProblemFinalTestVariantAssociation;
use App\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Persistent\Repository\ProblemFinal\ProblemFinalRepository")
 *
 * Class ProblemFinal
 * @package App\Model\Persistent\Entity
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
     * @ORM\ManyToOne(targetEntity="App\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate", cascade={"persist", "merge"})
     *
     * @var ProblemTemplate
     */
    protected $problemTemplate;

    /**
     * @ORM\OneToMany(targetEntity="App\Model\Persistent\Entity\ProblemFinalTestVariantAssociation", mappedBy="problemFinal", cascade={"all"})
     *
     * @var ProblemFinalTestVariantAssociation[]
     */
    protected $testVariantAssociations;

    /**
     * ProblemFinal constructor.
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
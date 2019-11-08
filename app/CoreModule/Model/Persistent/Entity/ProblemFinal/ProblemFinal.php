<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 12:29
 */

namespace App\CoreModule\Model\Persistent\Entity\ProblemFinal;

use App\CoreModule\Model\Persistent\Entity\Problem;
use App\CoreModule\Model\Persistent\Entity\ProblemFinalTestVariantAssociation;
use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Persistent\Repository\ProblemFinal\ProblemFinalRepository")
 *
 * Class ProblemFinal
 * @package App\CoreModule\Model\Persistent\Entity
 */
class ProblemFinal extends Problem
{
    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(
     *     type="integer",
     *     message="matchesIndex must be {{ type }}."
     * )
     *
     * @var int|null
     */
    protected $matchesIndex;

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
     * @ORM\ManyToOne(targetEntity="App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate", cascade={"persist", "merge"})
     *
     * @var ProblemTemplate
     */
    protected $problemTemplate;

    /**
     * @ORM\OneToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\ProblemFinalTestVariantAssociation", mappedBy="problemFinal", cascade={"all"})
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

    /**
     * @return int|null
     */
    public function getMatchesIndex(): ?int
    {
        return $this->matchesIndex;
    }

    /**
     * @param int|null $matchesIndex
     */
    public function setMatchesIndex(?int $matchesIndex): void
    {
        $this->matchesIndex = $matchesIndex;
    }

}
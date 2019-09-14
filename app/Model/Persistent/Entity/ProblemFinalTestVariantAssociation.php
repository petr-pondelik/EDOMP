<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 10:36
 */

namespace App\Model\Persistent\Entity;

use App\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
use App\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Persistent\Repository\ProblemFinalTestVariantAssociationRepository")
 *
 * Class ProblemTestAssociation
 * @package App\Model\Persistent\Entity
 */
class ProblemFinalTestVariantAssociation extends BaseEntity
{
    /**
     * @var string
     */
    protected $toStringAttr = 'successRate';

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(
     *     type="bool",
     *     message="NextPage must be {{ type }}."
     * )
     * @var bool
     */
    protected $nextPage = false;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Type(
     *     type="float",
     *     message="SuccessRate must be {{ type }}."
     * )
     * @var float
     */
    protected $successRate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Persistent\Entity\ProblemFinal\ProblemFinal", inversedBy="testVariantAssociations", cascade={"persist", "merge"})
     * @Assert\NotBlank(
     *     message="ProblemFinal can't be blank."
     * )
     *
     * @var ProblemFinal
     */
    protected $problemFinal;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate", cascade={"persist", "merge"})
     *
     * @var ProblemTemplate
     */
    protected $problemTemplate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Persistent\Entity\TestVariant", inversedBy="problemFinalAssociations", cascade={"persist", "merge"})
     * @Assert\NotBlank(
     *     message="TestVariant can't be blank."
     * )
     *
     * @var TestVariant
     */
    protected $testVariant;

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
    public function getSuccessRate(): ?float
    {
        return $this->successRate;
    }

    /**
     * @param float|null $successRate
     */
    public function setSuccessRate(float $successRate = null): void
    {
        $this->successRate = $successRate;
    }

    /**
     * @return ProblemFinal
     */
    public function getProblemFinal(): ProblemFinal
    {
        return $this->problemFinal;
    }

    /**
     * @param ProblemFinal $problemFinal
     */
    public function setProblemFinal(ProblemFinal $problemFinal): void
    {
        $this->problemFinal = $problemFinal;
    }

    /**
     * @return TestVariant
     */
    public function getTestVariant(): TestVariant
    {
        return $this->testVariant;
    }

    /**
     * @param TestVariant $testVariant
     */
    public function setTestVariant(TestVariant $testVariant): void
    {
        $this->testVariant = $testVariant;
    }

    /**
     * @return ProblemTemplate
     */
    public function getProblemTemplate(): ?ProblemTemplate
    {
        return $this->problemTemplate;
    }

    /**
     * @param ProblemTemplate|null $problemTemplate
     */
    public function setProblemTemplate(ProblemTemplate $problemTemplate = null): void
    {
        $this->problemTemplate = $problemTemplate;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:51
 */

namespace App\CoreModule\Model\Persistent\Entity\ProblemTemplate;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Persistent\Repository\ProblemTemplate\GeometricSequenceTemplateRepository")
 *
 * Class GeometricSequenceTemplate
 * @package App\CoreModule\Model\Persistent\Entity
 */
class GeometricSequenceTemplate extends ProblemTemplate
{
    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank(
     *     message="IndexVariable can't be blank."
     * )
     * @Assert\Type(
     *     type="string",
     *     message="IndexVariable must be {{ type }}."
     * )
     * @Assert\Length(
     *     min=1,
     *     max=1,
     *     exactMessage="IndexVariable must be string of length 1."
     * )
     *
     * @var string
     */
    protected $indexVariable;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\NotBlank(
     *     message="FirstN can't be blank."
     * )
     * @Assert\Type(
     *     type="integer",
     *     message="FirstN must be {{ type }}."
     * )
     *
     * @var int
     */
    protected $firstN;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Type(
     *     type="float",
     *     message="Quotient must be {{ type }}."
     * )
     *
     * @var float
     */
    protected $quotient;

    /**
     * @return int
     */
    public function getFirstN(): int
    {
        return $this->firstN;
    }

    /**
     * @param int $firstN
     */
    public function setFirstN(int $firstN): void
    {
        $this->firstN = $firstN;
    }

    /**
     * @return float|null
     */
    public function getQuotient(): ?float
    {
        return $this->quotient;
    }

    /**
     * @param float $quotient
     */
    public function setQuotient(float $quotient): void
    {
        $this->quotient = $quotient;
    }

    /**
     * @return string
     */
    public function getIndexVariable(): string
    {
        return $this->indexVariable;
    }

    /**
     * @param string $indexVariable
     */
    public function setIndexVariable(string $indexVariable): void
    {
        $this->indexVariable = $indexVariable;
    }
}
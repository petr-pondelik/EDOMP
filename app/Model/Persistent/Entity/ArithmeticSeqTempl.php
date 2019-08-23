<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:51
 */

namespace App\Model\Persistent\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Persistent\Repository\ArithmeticSeqTemplRepository")
 *
 * Class ArithmeticSeqTempl
 * @package App\Model\Persistent\Entity
 */
class ArithmeticSeqTempl extends ProblemTemplate
{
    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank(
     *     message="Variable can't be blank."
     * )
     * @Assert\Type(
     *     type="string",
     *     message="Variable must be {{ type }}."
     * )
     * @Assert\Length(
     *     min=1,
     *     max=1,
     *     exactMessage="Variable must be string of length 1."
     * )
     *
     * @var string
     */
    protected $variable;

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
     *     message="Difference must be {{ type }}."
     * )
     *
     * @var float
     */
    protected $difference;

    /**
     * @return string
     */
    public function getVariable(): string
    {
        return $this->variable;
    }

    /**
     * @param string $variable
     */
    public function setVariable(string $variable): void
    {
        $this->variable = $variable;
    }

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
    public function getDifference(): ?float
    {
        return $this->difference;
    }

    /**
     * @param float $difference
     */
    public function setDifference(float $difference): void
    {
        $this->difference = $difference;
    }
}
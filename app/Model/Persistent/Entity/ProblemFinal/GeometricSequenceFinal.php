<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.9.19
 * Time: 21:59
 */

namespace App\Model\Persistent\Entity\ProblemFinal;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Persistent\Repository\ProblemFinal\GeometricSequenceFinalRepository")
 *
 * Class GeometricSequenceFinal
 * @package App\Model\Persistent\Entity
 */
class GeometricSequenceFinal extends ProblemFinal
{
    /**
     * @ORM\Column(type="string", nullable=false, length=1)
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
     *     exactMessage="IndexVariable must be string of length 1.",
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
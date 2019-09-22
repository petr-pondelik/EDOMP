<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.9.19
 * Time: 21:55
 */

namespace App\Model\Persistent\Entity\ProblemFinal;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Persistent\Repository\ProblemFinal\LinearEquationFinalRepository")
 *
 * Class LinearEquationFinal
 * @package App\Model\Persistent\Entity
 */
class LinearEquationFinal extends ProblemFinal
{
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
}
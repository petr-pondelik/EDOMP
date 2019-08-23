<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:47
 */

namespace App\Model\Persistent\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Persistent\Repository\LinearEqTemplRepository")
 *
 * Class LinearEqTempl
 * @package App\Model\Persistent\Entity
 */
class LinearEqTempl extends ProblemTemplate
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
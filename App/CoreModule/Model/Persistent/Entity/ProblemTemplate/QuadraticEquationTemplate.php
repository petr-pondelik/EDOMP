<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:50
 */

namespace App\CoreModule\Model\Persistent\Entity\ProblemTemplate;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\CoreModule\Model\Persistent\Repository\ProblemTemplate\QuadraticEquationTemplateRepository")
 *
 * Class QuadraticEquationTemplate
 * @package App\CoreModule\Model\Persistent\Entity
 */
class QuadraticEquationTemplate extends ProblemTemplate
{
    /**
     * @ORM\Column(type="string", nullable=false, length=1)
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
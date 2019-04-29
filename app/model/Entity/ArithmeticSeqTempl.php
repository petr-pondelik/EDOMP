<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:51
 */

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\ArithmeticSeqTemplRepository")
 *
 * Class ArithmeticSeqTempl
 * @package App\Model\Entity
 */
class ArithmeticSeqTempl extends ProblemTemplate
{
    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $variable;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\NotBlank()
     *
     * @var int
     */
    protected $firstN;

    /**
     * @ORM\Column(type="integer", nullable=true)
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
     * @return float
     */
    public function getDifference(): float
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
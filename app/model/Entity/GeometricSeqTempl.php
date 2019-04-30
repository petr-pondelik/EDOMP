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
 * @ORM\Entity(repositoryClass="App\Model\Repository\GeometricSeqTemplRepository")
 *
 * Class GeometricSeqTempl
 * @package App\Model\Entity
 */
class GeometricSeqTempl extends ProblemTemplate
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
     * @ORM\Column(type="float", nullable=true)
     *
     * @var float
     */
    protected $quotient;

    /**
     * ProblemTemplate constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->isTemplate = true;
    }

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
    public function getQuotient(): float
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
}
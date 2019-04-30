<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:47
 */

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\LinearEqTemplRepository")
 *
 * Class LinearEqTempl
 * @package App\Model\Entity
 */
class LinearEqTempl extends ProblemTemplate
{
    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $variable;

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
}
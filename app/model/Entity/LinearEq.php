<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 18:42
 */

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\LinearEqRepository")
 *
 * Class LinearEq
 * @package App\Model\Entity
 */
class LinearEq extends Problem
{
    /**
     * @ORM\Column(type="string", nullable=true)
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
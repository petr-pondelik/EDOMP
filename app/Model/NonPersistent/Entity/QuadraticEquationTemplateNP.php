<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.8.19
 * Time: 21:45
 */

namespace App\Model\NonPersistent\Entity;

/**
 * Class QuadraticEquationTemplate
 * @package App\Model\NonPersistent\Entity\Entity
 */
class QuadraticEquationTemplateNP extends EquationTemplateNP
{
    /**
     * @var int
     */
    protected $rank = 2;

    /**
     * @var string|null
     */
    protected $discriminant;

    /**
     * @return string|null
     */
    public function getDiscriminant(): ?string
    {
        return $this->discriminant;
    }

    /**
     * @param string|null $discriminant
     */
    public function setDiscriminant(?string $discriminant): void
    {
        $this->discriminant = $discriminant;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.8.19
 * Time: 19:39
 */

namespace App\Model\NonPersistent\Entity;

/**
 * Class LinearEquationTemplate
 * @package App\Model\NonPersistent\Entity\Entity
 */
class LinearEquationTemplateNP extends ProblemTemplateNP
{
    /**
     * @var string|null
     */
    protected $linearVariableExpression;

    /**
     * @return string|null
     */
    public function getLinearVariableExpression(): ?string
    {
        return $this->linearVariableExpression;
    }

    /**
     * @param string|null $linearVariableExpression
     */
    public function setLinearVariableExpression(?string $linearVariableExpression): void
    {
        $this->linearVariableExpression = $linearVariableExpression;
    }

}
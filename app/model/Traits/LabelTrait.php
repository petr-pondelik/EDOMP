<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 16.5.19
 * Time: 15:54
 */

namespace App\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait LabelTrait
{
    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank(
     *     message="Label can't be blank."
     * )
     * @Assert\Type(
     *     type="string",
     *     message="Label must be {{ type }}."
     * )
     *
     * @var string
     */
    protected $label;

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }
}
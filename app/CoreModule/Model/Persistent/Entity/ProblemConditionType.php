<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 13:10
 */

namespace App\CoreModule\Model\Persistent\Entity;

use App\Model\Persistent\Traits\LabelTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Persistent\Repository\ProblemConditionTypeRepository")
 *
 * Class ProblemConditionType
 * @package App\CoreModule\Model\Persistent\Entity
 */
class ProblemConditionType extends BaseEntity
{
    use LabelTrait;

    /**
     * @var string
     */
    protected $toStringAttr = 'label';

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Type(
     *     type="string",
     *     message="Label must be {{ type }}."
     * )
     *
     * @var string|null
     */
    protected $prompt;

    /**
     * @ORM\OneToMany(targetEntity="ProblemCondition", mappedBy="problemConditionType", cascade={"persist", "merge"})
     */
    protected $problemConditions;

    /**
     * @ORM\ManyToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\ProblemType", mappedBy="conditionTypes", cascade={"persist", "merge"})
     */
    protected $problemTypes;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(
     *     type="bool",
     *     message="IsValidation must be {{ type }}."
     * )
     *
     * @var bool
     */
    protected $isValidation = false;

    /**
     * ProblemConditionType constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->problemConditions = new ArrayCollection();
        $this->problemTypes = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getProblemTypes()
    {
        return $this->problemTypes;
    }

    /**
     * @param mixed $problemTypes
     */
    public function setProblemTypes($problemTypes): void
    {
        $this->problemTypes = $problemTypes;
    }

    /**
     * @return mixed
     */
    public function getProblemConditions()
    {
        return $this->problemConditions;
    }

    /**
     * @param mixed $problemConditions
     */
    public function setProblemConditions($problemConditions): void
    {
        $this->problemConditions = $problemConditions;
    }

    /**
     * @return bool
     */
    public function isValidation(): bool
    {
        return $this->isValidation;
    }

    /**
     * @param bool $isValidation
     */
    public function setIsValidation(bool $isValidation): void
    {
        $this->isValidation = $isValidation;
    }

    /**
     * @return string|null
     */
    public function getPrompt(): ?string
    {
        return $this->prompt;
    }

    /**
     * @param string|null $prompt
     */
    public function setPrompt(?string $prompt): void
    {
        $this->prompt = $prompt;
    }
}
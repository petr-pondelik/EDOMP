<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 13:10
 */

namespace App\Model\Entity;

use App\Model\Traits\LabelTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\ProblemConditionTypeRepository")
 *
 * Class ProblemConditionType
 * @package App\Model\Entity
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
     * @var string
     */
    protected $prompt;

    /**
     * @ORM\OneToMany(targetEntity="ProblemCondition", mappedBy="problemConditionType", cascade={"persist", "merge"})
     */
    protected $problemConditions;

    /**
     * @ORM\ManyToMany(targetEntity="App\Model\Entity\ProblemType", mappedBy="conditionTypes", cascade={"persist", "merge"})
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
     * @throws \Exception
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
     * @return string
     */
    public function getPrompt(): string
    {
        return $this->prompt;
    }

    /**
     * @param string $prompt
     */
    public function setPrompt(string $prompt): void
    {
        $this->prompt = $prompt;
    }
}
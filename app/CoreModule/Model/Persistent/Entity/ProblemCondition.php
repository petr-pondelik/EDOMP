<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 13:19
 */

namespace App\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Traits\LabelTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\CoreModule\Model\Persistent\Repository\ProblemConditionRepository")
 *
 * Class ProblemCondition
 * @package App\CoreModule\Model\Persistent\Entity
 */
class ProblemCondition extends BaseEntity
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
     *     message="LabelFull must be {{ type }}."
     * )
     *
     * @var string|null
     */
    protected $labelFull;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\NotBlank(
     *     message="Accessor can't be blank."
     * )
     *
     * @var int
     */
    protected $accessor;

    /**
     * @ORM\ManyToOne(targetEntity="ValidationFunction", inversedBy="problemConditions", cascade={"persist", "merge"})
     * @Assert\NotBlank(
     *     message="ValidationFunction can't be blank."
     * )
     *
     * @var ValidationFunction|null
     */
    protected $validationFunction;

    /**
     * @ORM\ManyToOne(targetEntity="ProblemConditionType", inversedBy="problemConditions", cascade={"persist", "merge"})
     * @Assert\NotBlank(
     *     message="ProblemConditionType can't be blank."
     * )
     *
     * @var ProblemConditionType
     */
    protected $problemConditionType;

    /**
     * @ORM\ManyToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\ProblemFinal\ProblemFinal", mappedBy="conditions")
     */
    protected $problems;

    /**
     * ProblemCondition constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->problems = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getAccessor(): int
    {
        return $this->accessor;
    }

    /**
     * @param int $accessor
     */
    public function setAccessor(int $accessor): void
    {
        $this->accessor = $accessor;
    }

    /**
     * @return mixed
     */
    public function getProblems()
    {
        return $this->problems;
    }

    /**
     * @param mixed $problems
     */
    public function setProblems($problems): void
    {
        $this->problems = $problems;
    }

    /**
     * @return ProblemConditionType
     */
    public function getProblemConditionType(): ProblemConditionType
    {
        return $this->problemConditionType;
    }

    /**
     * @param ProblemConditionType $problemConditionType
     */
    public function setProblemConditionType(ProblemConditionType $problemConditionType): void
    {
        $this->problemConditionType = $problemConditionType;
    }

    /**
     * @return ValidationFunction|null
     */
    public function getValidationFunction(): ?ValidationFunction
    {
        return $this->validationFunction;
    }

    /**
     * @param ValidationFunction|null $validationFunction
     */
    public function setValidationFunction(?ValidationFunction $validationFunction): void
    {
        $this->validationFunction = $validationFunction;
    }

    /**
     * @return string|null
     */
    public function getLabelFull(): ?string
    {
        return $this->labelFull;
    }

    /**
     * @param string|null $labelFull
     */
    public function setLabelFull(?string $labelFull): void
    {
        $this->labelFull = $labelFull;
    }
}
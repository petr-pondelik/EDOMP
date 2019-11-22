<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 18:50
 */

namespace App\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
use App\CoreModule\Model\Persistent\Traits\LabelTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository")
 *
 * Class ProblemType
 * @package App\CoreModule\Model\Persistent\Entity
 */
class ProblemType extends BaseEntity
{
    use LabelTrait;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\Type(
     *     type="string",
     *     message="keyLabel must be {{ type }}."
     * )
     *
     * @var string
     */
    protected $keyLabel;

    /**
     * @var string
     */
    protected $toStringAttr = 'label';

    /**
     * @ORM\OneToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\ProblemFinal\ProblemFinal", mappedBy="problemType", cascade={"persist", "merge"})
     *
     * @var ArrayCollection|ProblemFinal[]
     */
    protected $problems;

    /**
     * @ORM\ManyToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\ProblemConditionType", inversedBy="problemTypes", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="problem_tp_problem_condition_tp_rel")
     */
    protected $conditionTypes;

    /**
     * ProblemType constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->problems = new ArrayCollection();
        $this->conditionTypes = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getProblems(): ?Collection
    {
        return $this->problems;
    }

    /**
     * @param Collection $problems
     */
    public function setProblems($problems): void
    {
        $this->problems = $problems;
    }

    /**
     * @return Collection/ProblemConditionType[]
     */
    public function getConditionTypes(): ?Collection
    {
        return $this->conditionTypes;
    }

    /**
     * @param mixed $conditionTypes
     */
    public function setConditionTypes($conditionTypes): void
    {
        $this->conditionTypes = $conditionTypes;
    }

    /**
     * @return string
     */
    public function getKeyLabel(): string
    {
        return $this->keyLabel;
    }

    /**
     * @param string $keyLabel
     */
    public function setKeyLabel(string $keyLabel): void
    {
        $this->keyLabel = $keyLabel;
    }
}
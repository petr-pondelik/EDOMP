<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 13:10
 */

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\DateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\ProblemConditionTypeRepository")
 *
 * Class ProblemConditionType
 * @package App\Model\Entity
 */
class ProblemConditionType
{
    //Identifier trait for id column
    use Identifier;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\NotBlank()
     *
     * @var int
     */
    protected $accessor;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $label;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @var DateTime
     */
    protected $created;

    /**
     * @ORM\OneToMany(targetEntity="ProblemCondition", mappedBy="conditionType", cascade={"persist", "merge"})
     */
    protected $conditions;

    /**
     * @ORM\ManyToMany(targetEntity="App\Model\Entity\ProblemType", mappedBy="conditionTypes", cascade={"persist", "merge"})
     */
    protected $problemTypes;

    /**
     * ProblemConditionType constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->created = new DateTime();
        $this->conditions = new ArrayCollection();
        $this->problemTypes = new ArrayCollection();
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

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     */
    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
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
}
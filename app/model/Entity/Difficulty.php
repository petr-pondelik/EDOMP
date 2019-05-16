<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 13:10
 */

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\DifficultyRepository")
 *
 * Class Difficulty
 * @package App\Model\Entity
 */
class Difficulty extends BaseEntity
{
    /**
     * @var string
     */
    protected $toStringAttr = "label";

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $label;

    /**
     * @ORM\OneToMany(targetEntity="ProblemFinal", mappedBy="difficulty", cascade={"persist", "merge"})
     *
     * @var Collection
     */
    protected $problems;

    /**
     * Difficulty constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->problems = new ArrayCollection();
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
     * @return Collection|null
     */
    public function getProblems(): ?Collection
    {
        return $this->problems;
    }

    /**
     * @param $problems
     * @return void
     */
    public function setProblems($problems): void
    {
        $this->problems = $problems;
    }
}
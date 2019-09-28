<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.9.19
 * Time: 16:58
 */

namespace App\Model\Persistent\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Persistent\Repository\FilterRepository")
 *
 * Class Filter
 * @package App\Model\Persistent\Entity
 */
class Filter extends BaseEntity
{
    /**
     * @ORM\Column(type="json_array", nullable=false)
     * @Assert\NotBlank(
     *     message="SelectedFilters can't be blank."
     * )
     *
     * @var iterable
     */
    protected $selectedFilters;

    /**
     * @ORM\Column(type="json_array", nullable=false)
     * @Assert\NotBlank(
     *     message="SelectedProblems can't be blank."
     * )
     *
     * @var iterable
     */
    protected $selectedProblems;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\Type(
     *     type="integer",
     *     message="Seq must be {{ type }}."
     * )
     * @Assert\NotBlank(
     *     message="Seq can't be blank."
     * )
     *
     * @var int
     */
    protected $seq;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Persistent\Entity\Test", inversedBy="filters", cascade={"persist", "merge"})
     * @Assert\NotBlank(
     *     message="Test can't be blank."
     * )
     *
     * @var Test
     */
    protected $test;

    /**
     * @ORM\ManyToMany(targetEntity="App\Model\Persistent\Entity\ProblemType")
     * @ORM\JoinTable(name="filter_problem_type_rel")
     *
     * @var ArrayCollection|PersistentCollection
     */
    protected $problemTypes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Model\Persistent\Entity\Difficulty")
     * @ORM\JoinTable(name="filter_difficulty_rel")
     *
     * @var ArrayCollection|PersistentCollection
     */
    protected $difficulties;

    /**
     * @ORM\ManyToMany(targetEntity="App\Model\Persistent\Entity\SubCategory")
     * @ORM\JoinTable(name="filter_sub_category_rel")
     *
     * @var ArrayCollection|PersistentCollection
     */
    protected $subCategories;

    /**
     * @ORM\ManyToMany(targetEntity="App\Model\Persistent\Entity\ProblemCondition")
     * @ORM\JoinTable(name="filter_problem_condition_rel")
     *
     * @var ArrayCollection|PersistentCollection
     */
    protected $problemConditions;

    /**
     * Filter constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->problemTypes = new ArrayCollection();
        $this->difficulties = new ArrayCollection();
        $this->subCategories = new ArrayCollection();
        $this->problemConditions = new ArrayCollection();
    }

    /**
     * @return Test
     */
    public function getTest(): Test
    {
        return $this->test;
    }

    /**
     * @param Test $test
     */
    public function setTest(Test $test): void
    {
        $this->test = $test;
    }

    /**
     * @return int
     */
    public function getSeq(): int
    {
        return $this->seq;
    }

    /**
     * @param int $seq
     */
    public function setSeq(int $seq): void
    {
        $this->seq = $seq;
    }

    /**
     * @return iterable
     */
    public function getSelectedFilters(): iterable
    {
        return $this->selectedFilters;
    }

    /**
     * @param iterable $selectedFilters
     */
    public function setSelectedFilters(iterable $selectedFilters): void
    {
        $this->selectedFilters = $selectedFilters;
    }

    /**
     * @return iterable
     */
    public function getSelectedProblems(): iterable
    {
        return $this->selectedProblems;
    }

    /**
     * @param iterable $selectedProblems
     */
    public function setSelectedProblems(iterable $selectedProblems): void
    {
        $this->selectedProblems = $selectedProblems;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getProblemTypes()
    {
        return $this->problemTypes;
    }

    /**
     * @param ArrayCollection|PersistentCollection $problemTypes
     */
    public function setProblemTypes($problemTypes): void
    {
        $this->problemTypes = $problemTypes;
    }

    /**
     * @param ProblemType $problemType
     */
    public function addProblemType(ProblemType $problemType): void
    {
        if($this->problemTypes->contains($problemType)){
            return;
        }
        $this->problemTypes[] = $problemType;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getDifficulties()
    {
        return $this->difficulties;
    }

    /**
     * @param ArrayCollection|PersistentCollection $difficulties
     */
    public function setDifficulties($difficulties): void
    {
        $this->difficulties = $difficulties;
    }

    /**
     * @param Difficulty $difficulty
     */
    public function addDifficulty(Difficulty $difficulty): void
    {
        if($this->difficulties->contains($difficulty)){
            return;
        }
        $this->difficulties[] = $difficulty;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getSubCategories()
    {
        return $this->subCategories;
    }

    /**
     * @param ArrayCollection|PersistentCollection $subCategories
     */
    public function setSubCategories($subCategories): void
    {
        $this->subCategories = $subCategories;
    }

    /**
     * @param SubCategory $subCategory
     */
    public function addSubCategory(SubCategory $subCategory): void
    {
        if($this->subCategories->contains($subCategory)){
            return;
        }
        $this->subCategories[] = $subCategory;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getProblemConditions()
    {
        return $this->problemConditions;
    }

    /**
     * @param ArrayCollection|PersistentCollection $problemConditions
     */
    public function setProblemConditions($problemConditions): void
    {
        $this->problemConditions = $problemConditions;
    }

    /**
     * @param ProblemCondition $problemCondition
     */
    public function addProblemCondition(ProblemCondition $problemCondition): void
    {
        if($this->problemConditions->contains($problemCondition)){
            return;
        }
        $this->problemConditions[] = $problemCondition;
    }
}
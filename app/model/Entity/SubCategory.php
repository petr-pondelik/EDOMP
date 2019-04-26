<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 15:05
 */

namespace App\Model\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\DateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\SubCategoryRepository")
 *
 * Class SubCategory
 * @package App\Model\Entity
 */
class SubCategory
{
    //Identifier trait for id column
    use Identifier;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $label;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @Assert\NotBlank()
     *
     * @var DateTime
     */
    protected $created;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\Category", inversedBy="subCategories", cascade={"persist"})
     *
     * @var Category
     */
    protected $category;

    /**
     * @ORM\OneToMany(targetEntity="App\Model\Entity\Problem", mappedBy="subCategory", cascade={"persist"})
     *
     * @var Collection
     */
    protected $problems;

    /**
     * SubCategory constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->created = new DateTime();
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
        return DateTime::from($this->created);
    }

    /**
     * @param DateTime $created
     */
    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
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
     * @return void
     */
    public function setProblems($problems): void
    {
        $this->problems = $problems;
    }

    /**
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }
}
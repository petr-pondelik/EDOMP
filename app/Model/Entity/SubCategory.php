<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 15:05
 */

namespace App\Model\Entity;

use App\Model\Traits\LabelTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\SubCategoryRepository")
 *
 * Class SubCategory
 * @package App\Model\Entity
 */
class SubCategory extends BaseEntity
{
    use LabelTrait;

    /**
     * @var string
     */
    protected $toStringAttr = "label";

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\Category", inversedBy="subCategories", cascade={"persist", "merge"})
     * @Assert\NotBlank(
     *     message="Category can't be blank."
     * )
     *
     * @var Category
     */
    protected $category;

    /**
     * @ORM\OneToMany(targetEntity="ProblemFinal", mappedBy="subCategory", cascade={"all"})
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
        parent::__construct();
        $this->problems = new ArrayCollection();
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
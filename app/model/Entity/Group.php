<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 21:46
 */

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\GroupRepository")
 * @ORM\Table(name="`group`")
 *
 * Class Group
 * @package App\Model\Entity
 */
class Group extends BaseEntity
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
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\SuperGroup", inversedBy="groups", cascade={"persist", "merge"})
     *
     * @var SuperGroup
     */
    protected $superGroup;

    /**
     * @ORM\ManyToMany(targetEntity="App\Model\Entity\User", mappedBy="groups", cascade={"persist", "merge"})
     */
    protected $users;

    /**
     * @ORM\ManyToMany(targetEntity="App\Model\Entity\Category", inversedBy="groups", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="group_category_rel")
     */
    protected $categories;

    /**
     * Group constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->users = new ArrayCollection();
        $this->categories = new ArrayCollection();
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
     * @return SuperGroup
     */
    public function getSuperGroup(): SuperGroup
    {
        return $this->superGroup;
    }

    /**
     * @param SuperGroup $superGroup
     */
    public function setSuperGroup(SuperGroup $superGroup): void
    {
        $this->superGroup = $superGroup;
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param mixed $categories
     */
    public function setCategories($categories): void
    {
        $this->categories = $categories;
    }

    /**
     * @param Category $category
     */
    public function addCategory(Category $category): void
    {
        if($this->categories->contains($category)) return;
        $this->categories[] = $category;
    }

    /**
     * @return array
     */
    public function getCategoriesId(): array
    {
        $res = [];
        foreach ($this->getCategories()->getValues() as $key => $category)
            array_push($res, $category->getId());
        return $res;
    }
}
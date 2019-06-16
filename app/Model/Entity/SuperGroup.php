<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 21:46
 */

namespace App\Model\Entity;

use App\Model\Traits\LabelTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\SuperGroupRepository")
 *
 * Class SuperGroup
 * @package App\Model\Entity
 */
class SuperGroup extends BaseEntity
{
    use LabelTrait;

    /**
     * @var string
     */
    protected $toStringAttr = 'label';

    /**
     * @ORM\OneToMany(targetEntity="App\Model\Entity\Group", mappedBy="superGroup", cascade={"all"})
     */
    protected $groups;

    /**
     * @ORM\ManyToMany(targetEntity="App\Model\Entity\Category", inversedBy="superGroups", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="super_group_category_rel")
     */
    protected $categories;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\User", inversedBy="superGroupsCreated", cascade={"all"})
     *
     * @var User
     */
    protected $createdBy;

    /**
     * SuperGroup constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->groups = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param mixed $groups
     */
    public function setGroups($groups): void
    {
        $this->groups = $groups;
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
     * @return array
     */
    public function getCategoriesId(): array
    {
        $res = [];
        foreach ($this->getCategories()->getValues() as $key => $category){
            $res[] = $category->getId();
        }
        return $res;
    }

    public function addCategory(Category $category): void
    {
        if($this->categories->contains($category)) return;
        $this->categories[] = $category;
    }

    /**
     * @return User|null
     */
    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy(User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }
}
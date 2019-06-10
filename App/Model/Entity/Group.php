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
    use LabelTrait;

    /**
     * @var string
     */
    protected $toStringAttr = 'label';

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\SuperGroup", inversedBy="groups", cascade={"persist", "merge"})
     * @Assert\NotBlank(
     *     message="SuperGroup can't be blank."
     * )
     *
     * @var SuperGroup
     */
    protected $superGroup;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\User", inversedBy="groupsCreated", cascade={"persist", "merge"})
     *
     * @var User
     */
    protected $createdBy;

    /**
     * @ORM\ManyToMany(targetEntity="App\Model\Entity\User", mappedBy="groups", cascade={"all"})
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
        foreach ($this->getCategories()->getValues() as $key => $category){
            $res[] = $category->getId();
        }
        return $res;
    }

    /**
     * @return User
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
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 21:46
 */

namespace App\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Traits\CreatedByTrait;
use App\CoreModule\Model\Persistent\Traits\LabelTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\CoreModule\Model\Persistent\Repository\GroupRepository")
 * @ORM\Table(name="`group`")
 *
 * Class Group
 * @package App\CoreModule\Model\Persistent\Entity
 */
class Group extends BaseEntity
{
    use LabelTrait;

    use CreatedByTrait;

    /**
     * @var string
     */
    protected $toStringAttr = 'label';

    /**
     * @ORM\ManyToOne(targetEntity="App\CoreModule\Model\Persistent\Entity\SuperGroup", inversedBy="groups", cascade={"persist", "merge"})
     * @Assert\NotBlank(
     *     message="SuperGroup can't be blank."
     * )
     *
     * @var SuperGroup
     */
    protected $superGroup;

    /**
     * @ORM\ManyToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\User", mappedBy="groups", cascade={"all"})
     */
    protected $users;

    /**
     * @ORM\ManyToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\Category", inversedBy="groups", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="group_category_rel")
     */
    protected $categories;

    /**
     * Group constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->teacherLevelSecured = true;
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
        if ($this->categories->contains($category)) {
            return;
        }
        $this->categories[] = $category;
    }

    /**
     * @return array
     */
    public function getCategoriesId(): array
    {
        $res = [];
        foreach ($this->getCategories()->getValues() as $key => $category) {
            $res[] = $category->getId();
        }
        return $res;
    }
}
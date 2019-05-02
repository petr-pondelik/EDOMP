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
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\DateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\SuperGroupRepository")
 *
 * Class SuperGroup
 * @package App\Model\Entity
 */
class SuperGroup
{
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
     * @ORM\OneToMany(targetEntity="App\Model\Entity\Group", mappedBy="superGroup", cascade={"all"})
     */
    protected $groups;

    /**
     * @ORM\ManyToMany(targetEntity="App\Model\Entity\Category", inversedBy="superGroups", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="super_group_category_rel")
     */
    protected $categories;

    /**
     * SuperGroup constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->created = new DateTime();
        $this->groups = new ArrayCollection();
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
     * @return string
     */
    public function __toString()
    {
        return $this->label;
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
        foreach ($this->getCategories()->getValues() as $key => $category)
            array_push($res, $category->getId());
        return $res;
    }

    public function addCategory(Category $category): void
    {
        if($this->categories->contains($category)) return;
        $this->categories[] = $category;
    }
}
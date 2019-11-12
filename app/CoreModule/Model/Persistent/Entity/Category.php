<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 15:54
 */

namespace App\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Traits\LabelTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Category
 * @package App\CoreModule\Model\Persistent\Entity
 *
 * @ORM\Entity(repositoryClass="App\CoreModule\Model\Persistent\Repository\CategoryRepository")
 */
class Category extends BaseEntity
{
    use LabelTrait;

    /**
     * @var string
     */
    protected $toStringAttr = 'label';

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank(
     *     message="Label can't be blank."
     * )
     *
     * @var string
     */
    protected $label;

    /**
     * @ORM\OneToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\SubCategory", mappedBy="category", cascade={"all"})
     */
    protected $subCategories;

    /**
     * @ORM\ManyToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\Group", mappedBy="categories", cascade={"persist", "merge"})
     */
    protected $groups;

    /**
     * @ORM\ManyToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\SuperGroup", mappedBy="categories", cascade={"persist", "merge"})
     */
    protected $superGroups;

    /**
     * Category constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->subCategories = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->superGroups = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getSubCategories(): ?Collection
    {
        return $this->subCategories;
    }

    /**
     * @param Collection $subCategories
     */
    public function setSubCategories(Collection $subCategories): void
    {
        $this->subCategories = $subCategories;
    }

    /**
     * @return string
     */
    public function __toString(): string
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
    public function getSuperGroups()
    {
        return $this->superGroups;
    }

    /**
     * @param mixed $superGroups
     */
    public function setSuperGroups($superGroups): void
    {
        $this->superGroups = $superGroups;
    }

}
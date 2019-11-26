<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 15:54
 */

namespace App\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Traits\CreatedByTrait;
use App\CoreModule\Model\Persistent\Traits\LabelTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Theme
 * @package App\CoreModule\Model\Persistent\Entity
 *
 * @ORM\Entity(repositoryClass="App\CoreModule\Model\Persistent\Repository\ThemeRepository")
 */
class Theme extends BaseEntity
{
    use LabelTrait;

    use CreatedByTrait;

    /**
     * @var string
     */
    protected $toStringAttr = 'label';

    /**
     * @ORM\OneToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\SubTheme", mappedBy="theme", cascade={"all"})
     */
    protected $subThemes;

    /**
     * @ORM\ManyToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\Group", mappedBy="themes", cascade={"persist", "merge"})
     */
    protected $groups;

    /**
     * @ORM\ManyToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\SuperGroup", mappedBy="themes", cascade={"persist", "merge"})
     */
    protected $superGroups;

    /**
     * Theme constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->teacherLevelSecured = true;
        $this->subThemes = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->superGroups = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getSubThemes(): ?Collection
    {
        return $this->subThemes;
    }

    /**
     * @param Collection $subThemes
     */
    public function setSubThemes(Collection $subThemes): void
    {
        $this->subThemes = $subThemes;
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
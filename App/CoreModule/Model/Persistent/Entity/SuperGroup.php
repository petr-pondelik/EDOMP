<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 21:46
 */

namespace App\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Traits\CreatedByTrait;
use App\CoreModule\Model\Persistent\Traits\KeyArrayTrait;
use App\CoreModule\Model\Persistent\Traits\LabelTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\CoreModule\Model\Persistent\Repository\SuperGroupRepository")
 *
 * Class SuperGroup
 * @package App\CoreModule\Model\Persistent\Entity
 */
class SuperGroup extends BaseEntity
{
    use LabelTrait;
    use CreatedByTrait;
    use KeyArrayTrait;

    /**
     * @var string
     */
    protected $toStringAttr = 'label';

    /**
     * @ORM\OneToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\Group", mappedBy="superGroup", cascade={"all"})
     */
    protected $groups;

    /**
     * @ORM\ManyToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\Theme", inversedBy="superGroups", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="super_group_theme_rel")
     */
    protected $themes;

    /**
     * SuperGroup constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->teacherLevelSecured = true;
        $this->groups = new ArrayCollection();
        $this->themes = new ArrayCollection();
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
    public function getThemes()
    {
        return $this->themes;
    }

    /**
     * @param mixed $themes
     */
    public function setThemes($themes): void
    {
        $this->themes = $themes;
    }

    public function addTheme(Theme $theme): void
    {
        if ($this->themes->contains($theme)) {
            return;
        }
        $this->themes[] = $theme;
    }
}
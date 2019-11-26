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
     * @ORM\ManyToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\Theme", inversedBy="groups", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="group_theme_rel")
     */
    protected $themes;

    /**
     * Group constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->teacherLevelSecured = true;
        $this->users = new ArrayCollection();
        $this->themes = new ArrayCollection();
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

    /**
     * @param Theme $theme
     */
    public function addTheme(Theme $theme): void
    {
        if ($this->themes->contains($theme)) {
            return;
        }
        $this->themes[] = $theme;
    }

    /**
     * @return array
     */
    public function getThemesId(): array
    {
        $res = [];
        foreach ($this->getThemes()->getValues() as $key => $theme) {
            $res[] = $theme->getId();
        }
        return $res;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param mixed $users
     */
    public function setUsers($users): void
    {
        $this->users = $users;
    }
}
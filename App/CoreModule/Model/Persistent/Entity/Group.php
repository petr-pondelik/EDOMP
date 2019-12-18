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
use Doctrine\Common\Collections\Collection;
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
     *
     * @var Collection
     */
    protected $users;

    /**
     * @ORM\ManyToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\Theme", inversedBy="groups", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="group_theme_rel")
     *
     * @var Collection
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
        /** @var Theme[] $themes */
        $themes = $this->getThemes()->getValues();
        foreach ($themes as $key => $theme) {
            $res[] = $theme->getId();
        }
        return $res;
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
     * @return Group
     */
    public function setSuperGroup(SuperGroup $superGroup): Group
    {
        $this->superGroup = $superGroup;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @param Collection $users
     * @return Group
     */
    public function setUsers(Collection $users): Group
    {
        $this->users = $users;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getThemes(): Collection
    {
        return $this->themes;
    }

    /**
     * @param Collection $themes
     * @return Group
     */
    public function setThemes(Collection $themes): Group
    {
        $this->themes = $themes;
        return $this;
    }
}
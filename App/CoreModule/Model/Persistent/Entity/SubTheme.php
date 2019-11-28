<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 15:05
 */

namespace App\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Traits\CreatedByTrait;
use App\CoreModule\Model\Persistent\Traits\LabelTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\CoreModule\Model\Persistent\Repository\SubThemeRepository")
 *
 * Class SubTheme
 * @package App\CoreModule\Model\Persistent\Entity
 */
class SubTheme extends BaseEntity
{
    use LabelTrait;

    use CreatedByTrait;

    /**
     * @var string
     */
    protected $toStringAttr = 'label';

    /**
     * @ORM\ManyToOne(targetEntity="App\CoreModule\Model\Persistent\Entity\Theme", inversedBy="subThemes", cascade={"persist", "merge"})
     * @Assert\NotBlank(
     *     message="Theme can't be blank."
     * )
     *
     * @var Theme
     */
    protected $theme;

    /**
     * @ORM\OneToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\ProblemFinal", mappedBy="subTheme", cascade={"all"})
     *
     * @var ArrayCollection|PersistentCollection
     */
    protected $problems;

    /**
     * SubTheme constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->teacherLevelSecured = true;
        $this->problems = new ArrayCollection();
    }

    /**
     * @return Theme
     */
    public function getTheme(): Theme
    {
        return $this->theme;
    }

    /**
     * @param Theme $theme
     */
    public function setTheme(Theme $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getProblems()
    {
        return $this->problems;
    }

    /**
     * @param ArrayCollection|PersistentCollection $problems
     */
    public function setProblems($problems): void
    {
        $this->problems = $problems;
    }
}
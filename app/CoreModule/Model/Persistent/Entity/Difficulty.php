<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 13:10
 */

namespace App\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Traits\LabelTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\CoreModule\Model\Persistent\Repository\DifficultyRepository")
 *
 * Class Difficulty
 * @package App\CoreModule\Model\Persistent\Entity
 */
class Difficulty extends BaseEntity
{
    use LabelTrait;

    /**
     * @var string
     */
    protected $toStringAttr = 'label';

    /**
     * @ORM\OneToMany(targetEntity="App\CoreModule\Model\Persistent\Entity\ProblemFinal\ProblemFinal", mappedBy="difficulty", cascade={"all"})
     *
     * @var ArrayCollection
     */
    protected $problems;

    /**
     * Difficulty constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->problems = new ArrayCollection();
    }

    /**
     * @return ArrayCollection|null
     */
    public function getProblems(): ?ArrayCollection
    {
        return $this->problems;
    }

    /**
     * @param $problems
     * @return void
     */
    public function setProblems($problems): void
    {
        $this->problems = $problems;
    }
}
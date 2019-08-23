<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 13:10
 */

namespace App\Model\Persistent\Entity;

use App\Model\Persistent\Traits\LabelTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Model\Persistent\Repository\DifficultyRepository")
 *
 * Class Difficulty
 * @package App\Model\Persistent\Entity
 */
class Difficulty extends BaseEntity
{
    use LabelTrait;

    /**
     * @var string
     */
    protected $toStringAttr = 'label';

    /**
     * @ORM\OneToMany(targetEntity="ProblemFinal", mappedBy="difficulty", cascade={"all"})
     *
     * @var ArrayCollection
     */
    protected $problems;

    /**
     * Difficulty constructor.
     * @throws \Exception
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
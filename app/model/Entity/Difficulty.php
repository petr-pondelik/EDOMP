<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 13:10
 */

namespace App\Model\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\DateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\ProblemRepository")
 *
 * Class Difficulty
 * @package App\Model\Entity
 */
class Difficulty
{
    //Identifier trait for id column
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
     * @ORM\OneToMany(targetEntity="App\Model\Entity\Problem", mappedBy="difficulty", cascade={"persist"})
     *
     * @var Collection
     */
    protected $problems;

    /**
     * Difficulty constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->created = new DateTime();
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
     * @return Collection|null
     */
    public function getProblems(): ?Collection
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

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     */
    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
    }
}
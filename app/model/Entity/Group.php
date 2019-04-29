<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 21:46
 */

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\DateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\GroupRepository")
 * @ORM\Table(name="`group`")
 *
 * Class Group
 * @package App\Model\Entity
 */
class Group
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
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\SuperGroup", cascade={"persist", "merge"})
     *
     * @var SuperGroup
     */
    protected $superGroup;

    /**
     * Group constructor.
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
}
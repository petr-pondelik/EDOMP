<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 15:54
 */

namespace App\Model\Entity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\DateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Category
 * @package App\Model\Entity
 *
 * @ORM\Entity(repositoryClass="App\Model\Repository\CategoryRepository")
 */
class Category
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
     * @ORM\OneToMany(targetEntity="App\Model\Entity\SubCategory", mappedBy="category", cascade={"persist"})
     *
     * @var Collection
     */
    protected $subCategories;

    /**
     * Category constructor.
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
        return $this->id;
    }

}
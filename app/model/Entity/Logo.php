<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 18:40
 */

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\DateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\LogoRepository")
 *
 * Class Logo
 * @package App\Model\Entity
 */
class Logo
{
    use Identifier;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $path;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $extension;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $extensionTmp;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\NotBlank()
     *
     * @var bool
     */
    protected $isUsed = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $label;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @var DateTime
     */
    protected $created;

    /**
     * Logo constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->created = new DateTime();
    }

    /**
     * @return string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getExtension(): ?string
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     */
    public function setExtension(string $extension): void
    {
        $this->extension = $extension;
    }

    /**
     * @return string
     */
    public function getExtensionTmp(): string
    {
        return $this->extensionTmp;
    }

    /**
     * @param string $extensionTmp
     */
    public function setExtensionTmp(string $extensionTmp): void
    {
        $this->extensionTmp = $extensionTmp;
    }


    /**
     * @return string
     */
    public function getLabel(): ?string
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
     * @return bool
     */
    public function isUsed(): bool
    {
        return $this->isUsed;
    }

    /**
     * @param bool $isUsed
     */
    public function setIsUsed(bool $isUsed): void
    {
        $this->isUsed = $isUsed;
    }

    public function __toString(): string
    {
        return "<img src='".$this->getPath()."'/>";
    }
}
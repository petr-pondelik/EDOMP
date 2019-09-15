<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 18:40
 */

namespace App\Model\Persistent\Entity;

use App\Model\Persistent\Traits\LabelTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Persistent\Repository\LogoRepository")
 *
 * Class Logo
 * @package App\Model\Persistent\Entity
 */
class Logo extends BaseEntity
{
    use LabelTrait;

    /**
     * @var string
     */
    protected $toStringAttr = 'path';

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
     * @Assert\NotBlank(
     *     message="ExtensionTmp can't be blank."
     * )
     *
     * @var string
     */
    protected $extensionTmp;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $isUsed = false;

    /**
     * @return bool
     */
    public function isPdf(): bool
    {
        return $this->extension === '.pdf';
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
}
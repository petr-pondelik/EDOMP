<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 18:40
 */

namespace App\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Traits\CreatedByTrait;
use App\CoreModule\Model\Persistent\Traits\LabelTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\CoreModule\Model\Persistent\Repository\LogoRepository")
 *
 * Class Logo
 * @package App\CoreModule\Model\Persistent\Entity
 */
class Logo extends BaseEntity
{
    use LabelTrait;

    use CreatedByTrait;

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
     * Logo constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->teacherLevelSecured = true;
    }

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
}
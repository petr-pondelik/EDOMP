<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 16:23
 */

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Symfony\Component\Validator\Constraints as Assert;
use Nette\Utils\DateTime;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\TemplateJsonDataRepository")
 *
 * Class TemplateJsonData
 * @package App\Model\Entity
 */
class TemplateJsonData
{
    use Identifier;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $jsonData;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\NotBlank()
     *
     * @var int
     */
    protected $templateId;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @Assert\NotBlank()
     *
     * @var DateTime
     */
    protected $created;

    /**
     * TemplateJsonData constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->created = new DateTime();
    }

    /**
     * @return string
     */
    public function getJsonData(): string
    {
        return $this->jsonData;
    }

    /**
     * @param string $jsonData
     */
    public function setJsonData(string $jsonData): void
    {
        $this->jsonData = $jsonData;
    }

    /**
     * @return int
     */
    public function getTemplateId(): int
    {
        return $this->templateId;
    }

    /**
     * @param int $templateId
     */
    public function setTemplateId(int $templateId): void
    {
        $this->templateId = $templateId;
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
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 16:23
 */

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\TemplateJsonDataRepository")
 *
 * Class TemplateJsonData
 * @package App\Model\Entity
 */
class TemplateJsonData extends BaseEntity
{
    /**
     * @var string
     */
    protected $toStringAttr = "jsonData";

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Assert\Type(
     *     type="string",
     *     message="JsonData must be {{ type }}."
     * )
     * @var string
     */
    protected $jsonData;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\NotBlank(
     *     message="TemplateId can't be blank."
     * )
     * @Assert\Type(
     *     type="int",
     *     message="TamplateId must be {{ type }}."
     * )
     *
     * @var int
     */
    protected $templateId;

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
}
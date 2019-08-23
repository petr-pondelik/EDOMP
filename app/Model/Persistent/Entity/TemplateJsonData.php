<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 16:23
 */

namespace App\Model\Persistent\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Persistent\Repository\TemplateJsonDataRepository")
 *
 * Class TemplateJsonData
 * @package App\Model\Persistent\Entity
 */
class TemplateJsonData extends BaseEntity
{
    /**
     * @var string
     */
    protected $toStringAttr = 'jsonData';

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
     *     message="TemplateId must be {{ type }}."
     * )
     *
     * @var int
     */
    protected $templateId;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(
     *     type="bool",
     *     message="IsValidation must be {{ type }}."
     * )
     *
     * @var bool
     */
    protected $isValidation;

    /**
     * @return string
     */
    public function getJsonData()
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
     * @return bool
     */
    public function isValidation(): bool
    {
        return $this->isValidation;
    }

    /**
     * @param bool $isValidation
     */
    public function setIsValidation(bool $isValidation): void
    {
        $this->isValidation = $isValidation;
    }
}
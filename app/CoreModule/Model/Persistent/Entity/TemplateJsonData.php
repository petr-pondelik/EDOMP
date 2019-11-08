<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 16:23
 */

namespace App\CoreModule\Model\Persistent\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Persistent\Repository\TemplateJsonDataRepository")
 *
 * Class TemplateJsonData
 * @package App\CoreModule\Model\Persistent\Entity
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
     * @ORM\ManyToOne(targetEntity="App\CoreModule\Model\Persistent\Entity\ProblemConditionType", cascade={"persist", "merge"})
     *
     * @var ProblemConditionType
     */
    protected $problemConditionType;

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
     * @return ProblemConditionType
     */
    public function getProblemConditionType(): ProblemConditionType
    {
        return $this->problemConditionType;
    }

    /**
     * @param ProblemConditionType $problemConditionType
     */
    public function setProblemConditionType(ProblemConditionType $problemConditionType): void
    {
        $this->problemConditionType = $problemConditionType;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 12:29
 */

namespace App\Model\Entity;

use App\Model\Traits\ToStringTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\DateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\ProblemFinalRepository")
 *
 * Class ProblemFinal
 * @package App\Model\Entity
 */
class ProblemFinal extends Problem
{
    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $result;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\NotBlank()
     *
     * @var bool
     */
    protected $isGenerated = false;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\NotBlank()
     *
     * @var bool
     */
    protected $isUsed = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Entity\ProblemTemplate", cascade={"persist", "merge"})
     *
     * @var ProblemTemplate
     */
    protected $problemTemplate;

    /**
     * ProblemFinal constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->isTemplate = false;
    }

    /**
     * @return string
     */
    public function getResult(): ?string
    {
        return $this->result;
    }

    /**
     * @param string $result
     */
    public function setResult(string $result): void
    {
        $this->result = $result;
    }

    /**
     * @return bool
     */
    public function isGenerated(): bool
    {
        return $this->isGenerated;
    }

    /**
     * @param bool $isGenerated
     */
    public function setIsGenerated(bool $isGenerated): void
    {
        $this->isGenerated = $isGenerated;
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

    /**
     * @return ProblemTemplate
     */
    public function getProblemTemplate(): ProblemTemplate
    {
        return $this->problemTemplate;
    }

    /**
     * @param ProblemTemplate $problemTemplate
     */
    public function setProblemTemplate(ProblemTemplate $problemTemplate): void
    {
        $this->problemTemplate = $problemTemplate;
    }

}
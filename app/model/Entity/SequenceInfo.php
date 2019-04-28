<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 20:28
 */

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\SequenceInfoRepository")
 *
 * Class SequenceInfo
 * @package App\Model\Entity
 */
class SequenceInfo
{
    use Identifier;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\NotBlank()
     *
     * @var int
     */
    protected $problemTemplateSeqVal;

    /**
     * SequenceInfo constructor.
     * @param int $lastId
     */
    public function __construct(int $lastId)
    {
        $this->problemTemplateSeqVal = $lastId;
    }

    /**
     * @return int
     */
    public function getProblemTemplateSeqVal(): int
    {
        return $this->problemTemplateSeqVal;
    }

    /**
     * @param int $problemTemplateSeqVal
     */
    public function setProblemTemplateSeqVal(int $problemTemplateSeqVal): void
    {
        $this->problemTemplateSeqVal = $problemTemplateSeqVal;
    }
}
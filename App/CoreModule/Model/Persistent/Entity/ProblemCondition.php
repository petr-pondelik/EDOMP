<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 13:19
 */

namespace App\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Traits\LabelTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\CoreModule\Model\Persistent\Repository\ProblemConditionRepository")
 *
 * Class ProblemCondition
 * @package App\CoreModule\Model\Persistent\Entity
 */
class ProblemCondition extends BaseEntity
{
    use LabelTrait;

    /**
     * @var string
     */
    protected $toStringAttr = 'label';

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\NotBlank(
     *     message="Accessor can't be blank."
     * )
     *
     * @var int
     */
    protected $accessor;

    /**
     * @ORM\ManyToOne(targetEntity="ProblemConditionType", inversedBy="problemConditions", cascade={"persist", "merge"})
     * @Assert\NotBlank(
     *     message="ProblemConditionType can't be blank."
     * )
     *
     * @var ProblemConditionType
     */
    protected $problemConditionType;

    /**
     * @return int
     */
    public function getAccessor(): int
    {
        return $this->accessor;
    }

    /**
     * @param int $accessor
     */
    public function setAccessor(int $accessor): void
    {
        $this->accessor = $accessor;
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
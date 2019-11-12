<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 15.9.19
 * Time: 18:28
 */

namespace App\TeacherModule\Model\NonPersistent\ProblemDuplicity;


use App\CoreModule\Model\Persistent\Entity\Problem;

/**
 * Class DuplicityState
 * @package App\TeacherModule\Model\ProblemDuplicityModel
 */
abstract class DuplicityState
{
    /**
     * @var array
     */
    protected $used;

    /**
     * @var array
     */
    protected $free;

    /**
     * DuplicityState constructor.
     */
    public function __construct()
    {
        $this->used = [];
        $this->free = [];
    }

    /**
     * @return bool
     */
    abstract public function freeExists(): bool;

    /**
     * @param Problem $problem
     * @return bool
     */
    abstract public function addUsed(Problem $problem): bool;

    /**
     * @return array
     */
    public function getUsed(): array
    {
        return $this->used;
    }

    /**
     * @param array $used
     */
    public function setUsed(array $used): void
    {
        $this->used = $used;
    }

    /**
     * @return array
     */
    public function getFree(): array
    {
        return $this->free;
    }

    /**
     * @param Problem[] $free
     */
    public function setFree(array $free): void
    {
        $this->free = [];
        foreach ($free as $problem) {
            if(!isset($this->used[$problem->getId()])){
                $this->free[$problem->getId()] = true;
            }
        }
    }
}
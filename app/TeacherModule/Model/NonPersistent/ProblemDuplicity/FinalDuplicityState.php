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
 * Class FinalDuplicityState
 * @package App\TeacherModule\Model\ProblemDuplicityModel
 */
class FinalDuplicityState extends DuplicityState
{
    /**
     * @return bool
     */
    public function freeExists(): bool
    {
        foreach ($this->free as $problem){
            if($problem){
                return true;
            }
        }
        return false;
    }

    /**
     * @param Problem $problem
     * @return bool
     */
    public function addUsed(Problem $problem): bool
    {
        if(!isset($this->used[$problem->getId()])){
            $this->used[$problem->getId()] = $problem->getId();
            return true;
        }
        $this->free[$problem->getId()] = false;
        return false;
    }
}
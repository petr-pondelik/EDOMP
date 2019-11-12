<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 14.7.19
 * Time: 18:16
 */

namespace App\TeacherModule\Components\ProblemStack;


/**
 * Interface IProblemStackFactory
 * @package App\TeacherModule\Components\ProblemStack
 */
interface IProblemStackFactory
{
    /**
     * @param int $id
     * @return ProblemStackControl
     */
    public function create(int $id): ProblemStackControl;
}
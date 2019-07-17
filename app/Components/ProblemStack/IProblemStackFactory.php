<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 14.7.19
 * Time: 18:16
 */

namespace App\Components\ProblemStack;

/**
 * Interface IProblemStackFactory
 * @package App\Components\ProblemStack
 */
interface IProblemStackFactory
{
    /**
     * @return ProblemStackControl
     */
    public function create(): ProblemStackControl;
}
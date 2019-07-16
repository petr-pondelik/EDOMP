<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 14.7.19
 * Time: 18:16
 */

namespace App\Components\ProblemDragAndDrop;

/**
 * Interface IProblemDragAndDropFactory
 * @package App\Components\ProblemDragAndDrop
 */
interface IProblemDragAndDropFactory
{
    /**
     * @return ProblemDragAndDropControl
     */
    public function create(): ProblemDragAndDropControl;
}
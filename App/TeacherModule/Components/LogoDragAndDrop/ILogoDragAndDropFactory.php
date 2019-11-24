<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 17.7.19
 * Time: 21:01
 */

namespace App\TeacherModule\Components\LogoDragAndDrop;

/**
 * Interface ILogoDragAndDropFactory
 * @package App\TeacherModule\Components\LogoDragAndDrop
 */
interface ILogoDragAndDropFactory
{
    /**
     * @return LogoDragAndDropControl
     */
    public function create(): LogoDragAndDropControl;
}
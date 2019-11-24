<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.9.19
 * Time: 14:46
 */

namespace App\TeacherModule\Components\FilterView;

/**
 * Class IFilterTableFactory
 * @package App\TeacherModule\Components\FilterView
 */
interface IFilterViewFactory
{
    /**
     * @return FilterViewControl
     */
    public function create(): FilterViewControl;
}
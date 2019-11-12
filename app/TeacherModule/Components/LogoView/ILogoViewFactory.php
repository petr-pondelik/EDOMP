<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.9.19
 * Time: 17:06
 */

namespace App\TeacherModule\Components\LogoView;


/**
 * Interface ILogoViewFactory
 * @package App\TeacherModule\Components\LogoView
 */
interface ILogoViewFactory
{
    /**
     * @return LogoViewControl
     */
    public function create(): LogoViewControl;
}
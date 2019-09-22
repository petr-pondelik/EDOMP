<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.9.19
 * Time: 17:06
 */

namespace App\Components\LogoView;


/**
 * Interface ILogoViewFactory
 * @package App\Components\LogoView
 */
interface ILogoViewFactory
{
    /**
     * @return LogoViewControl
     */
    public function create(): LogoViewControl;
}
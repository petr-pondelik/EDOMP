<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 22.5.19
 * Time: 17:43
 */

namespace App\CoreModule\Components\SideBar;


/**
 * Interface ISideBarFactory
 * @package App\CoreModule\Components\SideBar
 */
interface ISideBarFactory
{
    /**
     * @return SideBarControl
     */
    public function create(): SideBarControl;
}
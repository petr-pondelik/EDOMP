<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 22.5.19
 * Time: 17:43
 */

namespace App\Components\SideBar;


/**
 * Interface ISideBarFactory
 * @package App\Components\SideBar
 */
interface ISideBarFactory
{
    /**
     * @return SideBarControl
     */
    public function create(): SideBarControl;
}
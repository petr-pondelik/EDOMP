<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 22.5.19
 * Time: 17:43
 */

namespace App\Components\SideBar;

use App\Components\IControlFactory;

/**
 * Class SideBarFactory
 * @package App\Components\SideBar
 */
class SideBarFactory implements IControlFactory
{
    /**
     * @return SideBarControl
     */
    public function create(): SideBarControl
    {
        return new SideBarControl();
    }
}
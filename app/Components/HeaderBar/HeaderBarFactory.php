<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 22.5.19
 * Time: 16:55
 */

namespace App\Components\HeaderBar;

use App\Components\IControlFactory;

/**
 * Class HeaderBarFactory
 * @package App\Components\HeaderBar
 */
class HeaderBarFactory implements IControlFactory
{
    /**
     * @return HeaderBarControl
     */
    public function create(): HeaderBarControl
    {
        return new HeaderBarControl();
    }
}
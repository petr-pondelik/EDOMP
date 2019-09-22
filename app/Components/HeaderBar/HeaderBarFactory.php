<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 22.5.19
 * Time: 16:55
 */

namespace App\Components\HeaderBar;


/**
 * Class HeaderBarFactory
 * @package App\Components\HeaderBar
 */
interface HeaderBarFactory
{
    /**
     * @return HeaderBarControl
     */
    public function create(): HeaderBarControl;
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 22.5.19
 * Time: 16:55
 */

namespace App\CoreModule\Components\HeaderBar;


/**
 * Interface IHeaderBarFactory
 * @package App\CoreModule\Components\HeaderBar
 */
interface IHeaderBarFactory
{
    /**
     * @return HeaderBarControl
     */
    public function create(): HeaderBarControl;
}
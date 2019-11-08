<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.9.19
 * Time: 14:46
 */

namespace App\Components\FilterTable;

/**
 * Class IFilterTableFactory
 * @package App\Components\FilterTable
 */
interface IFilterViewFactory
{
    /**
     * @return FilterViewControl
     */
    public function create(): FilterViewControl;
}
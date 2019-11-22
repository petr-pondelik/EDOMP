<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 22.11.19
 * Time: 15:29
 */

namespace App\CoreModule\Interfaces;

/**
 * Interface IParser
 * @package App\CoreModule\Interfaces
 */
interface IParser
{
    /**
     * @param string $input
     * @return string
     */
    public static function parse(string $input): string;
}
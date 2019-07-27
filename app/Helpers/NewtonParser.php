<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.7.19
 * Time: 20:59
 */

namespace App\Helpers;

use Nette\Utils\Strings;

/**
 * Class NewtonParser
 * @package App\Helpers
 */
class NewtonParser
{
    /**
     * @param string $expression
     * @return string
     */
    public static function newtonFractions(string $expression): string
    {
        return Strings::replace($expression, '~(\/)~', '(over)');
    }

    /**
     * @param string $expression
     * @return string
     */
    public static function newtonFormat(string $expression): string
    {
        $expression = self::newtonFractions($expression);
        return $expression;
    }
}
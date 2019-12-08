<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.3.19
 * Time: 18:39
 */

namespace App\CoreModule\Helpers;

use Nette\Utils\Strings;

/**
 * Class StringsHelper
 * @package App\CoreModule\Helpers
 */
final class StringsHelper
{
    public const LATEX_INLINE = 'latexInline';
    public const BRACKETS_SIMPLE = 'bracketsSimple';
    public const ADDITION = 'addition';
    public const SUBTRACTION = 'subtraction';

    protected const PREFIXES = [
        'latexInline' => '\\(',
        'bracketsSimple' => '(',
        'addition' => '+',
        'subtraction' => '-'
    ];

    protected const SUFFIXES = [
        'latexInline' => '\\)',
        'bracketsSimple' => ')',
        'addition' => '+',
        'subtraction' => '-'
    ];

    /**
     * @param string $expression
     * @return string
     */
    public static function removeWhiteSpaces(string $expression): string
    {
        return Strings::replace($expression, '~\s~', '');
    }

    /**
     * @param string $expression
     * @return string
     */
    public static function deduplicateWhiteSpaces(string $expression): string
    {
        return Strings::replace($expression, '~\s{2,}~', ' ');
    }

    /**
     * @param string $expression
     * @param bool $onlyAddition
     * @return string
     */
    public static function trimOperators(string $expression, bool $onlyAddition = false): string
    {
        $expression = self::trim($expression, self::ADDITION);
        if (!$onlyAddition) {
            $expression = self::trim($expression, self::SUBTRACTION);
        }
        return $expression;
    }

    /**
     * @param string $expression
     * @return string
     */
    public static function normalizeOperators(string $expression): string
    {
        $expression = Strings::replace($expression, '~\-\s*\-~', ' + ');
        $expression = Strings::replace($expression, '~\+\s*\+~', ' + ');
        $expression = Strings::replace($expression, '~\-\s*\+~', ' - ');
        $expression = Strings::replace($expression, '~\+\s*\-~', ' - ');
        $expression = Strings::replace($expression, '~\(\s*\+~', '(');
        $expression = self::trimOperators($expression, true);
        $expression = self::deduplicateWhiteSpaces($expression);
        return $expression;
    }

//    /**
//     * @param string $expression
//     * @return string
//     */
//    public static function deduplicateBrackets(string $expression): string
//    {
//        $expression = Strings::replace($expression, '~' . self::PREFIXES['latexInline'] . '{2,}' . '~', self::PREFIXES['bracketsSimple']);
//        $expression = Strings::replace($expression, '~' . self::SUFFIXES['latexInline'] . '{2,}' . '~', self::SUFFIXES['bracketsSimple']);
//        return $expression;
//    }

    /**
     * @param string $expression
     * @param string $type
     * @return string
     */
    public static function trim(string $expression, $type = self::BRACKETS_SIMPLE): string
    {
        $res = Strings::trim($expression);
        if (Strings::startsWith($res, self::PREFIXES[$type])) {
            $res = Strings::after($res, self::PREFIXES[$type]);
        }
        if (Strings::endsWith($res, self::SUFFIXES[$type])) {
            $res = Strings::before($res, self::SUFFIXES[$type], -1);
        }
        return Strings::trim($res);
    }

    /**
     * @param string $expression
     * @param string $wrapper
     * @return string
     */
    public static function wrap(string $expression, $wrapper = self::BRACKETS_SIMPLE): string
    {
        return self::PREFIXES[$wrapper] . $expression . self::SUFFIXES[$wrapper];
    }

    /**
     * @param string $expression
     * @param string $variable
     * @return string
     */
    public static function fillMultipliers(string $expression, string $variable = null): string
    {
        if ($variable) {
            $expression = Strings::replace($expression, '~(\d)(' . $variable . ')~', '$1*$2');
            $expression = Strings::replace($expression, '~(\d)(' . $variable . ')~', '$1*$2');
            $expression = Strings::replace($expression, '~(\d)\s*(' . $variable . ')~', '$1*$2');
            $expression = Strings::replace($expression, '~(\s*\))(' . $variable . ')~', '$1*$2');
        }
        $expression = Strings::replace($expression, '~(\-?p?\d+)\s+(\-?p?\d+)~', '$1*$2');
        $expression = Strings::replace($expression, '~(\))\s*(p\d+)~', '$1*$2');
        $expression = Strings::replace($expression, '~(\-?p?\d+)\*(\-?p?\d+)\s+(\-?p?\d+)~', '$1*$2*$3');
        return $expression;
    }

    /**
     * @param string $str
     * @param string $substr
     * @return string
     */
    public static function removeSubstring(string $str, string $substr): string
    {
        $before = Strings::before($str, $substr);
        $after = Strings::after($str, $substr);
        return self::deduplicateWhiteSpaces($before . $after);
    }
}
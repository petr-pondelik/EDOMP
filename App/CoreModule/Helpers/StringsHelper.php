<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.3.19
 * Time: 18:39
 */

namespace App\CoreModule\Helpers;

use App\TeacherModule\Exceptions\EquationException;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class StringsHelper
 * @package App\CoreModule\Helpers
 */
class StringsHelper
{
    public const LATEX_INLINE = 'latexInline';
    public const BRACKETS_SIMPLE = 'bracketsSimple';
    public const ADDITION = 'addition';
    public const SUBTRACTION = 'subtraction';

    public const IS_ADDITION = 1;
    public CONST IS_SUBTRACTION = 2;

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
     * @return string
     */
    public static function normalizeOperators(string $expression): string
    {
        $expression = Strings::replace($expression, '~\-\s*\-~', ' + ');
        $expression = Strings::replace($expression, '~\+\s*\+~', ' + ');
        $expression = Strings::replace($expression, '~\-\s*\+~', ' - ');
        $expression = Strings::replace($expression, '~\+\s*\-~', ' - ');
        $expression = Strings::replace($expression, '~\(\s*\+~', '(');
        $expression = self::deduplicateWhiteSpaces($expression);
        $expression = self::trimOperators($expression, true);
        return $expression;
    }

    /**
     * @param string $expression
     * @return string
     */
    public static function deduplicateBrackets(string $expression): string
    {
        $expression = Strings::replace($expression, '~' . self::PREFIXES['latexInline'] . '{2,}' . '~', self::PREFIXES['bracketsSimple']);
        $expression = Strings::replace($expression, '~' . self::SUFFIXES['latexInline'] . '{2,}' . '~', self::SUFFIXES['bracketsSimple']);
        return $expression;
    }

    /**
     * @param string $expression
     * @return string
     */
    public static function negateOperators(string $expression): string
    {
        $startOp = '';
        if (self::firstOperator($expression) === '+') {
            $startOp = '-';
        }
        $expression = self::trimOperators($expression);
        $expressionLen = strlen($expression);
        for ($i = 0; $i < $expressionLen; $i++) {
            if ($expression[$i] === '+') {
                $expression[$i] = '-';
                continue;
            }
            if ($expression[$i] === '-') {
                $expression[$i] = '+';
            }
        }
        return $startOp . $expression;
    }

    /**
     * @param string $expression
     * @param bool $returnAddition
     * @return string
     */
    public static function firstOperator(string $expression, bool $returnAddition = true): string
    {
        if (!Strings::startsWith($expression, '+') && !Strings::startsWith($expression, '-')) {
            return $returnAddition ? '+' : '';
        }
        $addInx = Strings::indexOf($expression, '+');
        $subInx = Strings::indexOf($expression, '-');
        if ($addInx === false) {
            return '-';
        }
        if ($subInx === false) {
            return $returnAddition ? '+' : '-';
        }
        if ($addInx < $subInx) {
            return $returnAddition ? '+' : '';
        }
        return '-';
    }

    /**
     * @param string $expression
     * @return int
     */
    public static function startOperator(string $expression): int
    {
        $expression = Strings::trim($expression);
        if (!Strings::startsWith($expression, '+') && !Strings::startsWith($expression, '-')) {
            return self::IS_ADDITION;
        }
        return self::firstOperator($expression);
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
     * @return string
     */
    public static function extractSequenceName(string $expression): string
    {
        return (Strings::match($expression, '~^\s*(\w*)\w$~'))[1];
    }

    /**
     * @param string $expression
     * @param bool $validate
     * @return ArrayHash
     * @throws EquationException
     */
    public static function getEquationSides(string $expression, bool $validate = true): ArrayHash
    {
        if ($validate && !self::isEquation($expression)) {
            throw new EquationException('Zadaný výraz není validní rovnicí.');
        }
        $sides = Strings::split($expression, '~=~');
        return ArrayHash::from([
            'left' => Strings::trim($sides[0]),
            'right' => Strings::trim($sides[1])
        ]);
    }

    /**
     * @param ArrayHash $sides
     * @return string
     */
    public static function mergeEqSides(ArrayHash $sides): string
    {
        if ($sides->left === '0') {
            return $sides->left;
        }
        return $sides->left . ' - (' . $sides->right . ')';
    }

    /**
     * @param string $expression
     * @return string
     */
    public static function standardizeOperators(string $expression): string
    {
        $expression = Strings::replace($expression, '~--~', '');
        return $expression;
    }

    /**
     * @param string $expression
     * @param string $variable
     * @return string
     */
    public static function getLinearVariableExpresion(string $expression, string $variable): string
    {
        $split = Strings::split($expression, '~(' . $variable . ')~');

        if (!$split[2]) {
            return '0';
        }

        foreach ($split as $key => $item) {
            $split[$key] = Strings::trim($item);
        }

        // Check for expr. x / expr. format
        if (Strings::startsWith($split[2], '/')) {
            $multiplier = $split[0] === '' ? '1' : $split[0];
            $multiplier = Strings::trim($multiplier) === '-' ? '-1' : $multiplier;

            $divNeg = Strings::trim(Strings::after($split[2], '/'));
            $divNeg = self::negateOperators($divNeg);

            $rightSide = sprintf('(%s)/(%s)', $divNeg, $multiplier);
        } else {
            $split[2] = self::negateOperators($split[2]);

            // Check if variable multiplier exists
            if ($split[0]) {
                $rightSide = sprintf('(%s)/(%s)', $split[2], $split[0]);
            } else {
                $rightSide = sprintf('(%s)', $split[2]);
            }
        }

        return self::fillMultipliers($rightSide);
    }

    /**
     * @param array $split
     * @param string $variable
     * @return bool
     */
    public static function containsVariable(array $split, string $variable): bool
    {
        foreach ($split as $part) {
            if ($part !== '' && !Strings::startsWith($part, '<par')) {
                if (Strings::contains($part, $variable)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param string $expression
     * @return bool
     */
    public static function isEquation(string $expression): bool
    {
        $split = Strings::split($expression, '~=~');
        if (count($split) !== 2 || Strings::match($split[0], '~^\s*$~') || Strings::match($split[1], '~^\s*$~')) {
            return false;
        }
        if (Strings::match($expression, '~^\s+=\s+~')) {
            return false;
        }
        return true;
    }

    /**
     * @param string $expression
     * @param string $variable
     * @return bool
     */
    public static function isSequence(string $expression, string $variable): bool
    {
        if (!Strings::match($expression, '~^\s*\w' . $variable . '\s*=~')) {
            return false;
        }
        return true;
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
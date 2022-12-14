<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.4.19
 * Time: 19:40
 */

namespace App\TeacherModule\Services;

use App\CoreModule\Helpers\RegularExpressions;
use App\CoreModule\Helpers\StringsHelper;
use App\CoreModule\Interfaces\IParser;
use Nette\Utils\Strings;

/**
 * Class LatexParser
 * @package App\CoreModule\Helpers
 */
final class LatexParser implements IParser
{
    protected const GLOBAL = 'global';

    protected const INLINE = 'inline';

    protected const DISPLAY = 'display';

    protected const PARENTHESES = 'parentheses';

    protected const PREFIXES = [

        'global' => [

            'inline' => [

                0 => [
                    'plain' => '\(',
                    'original' => '\\\\\(',
                    'replacement' => ''
                ],

                1 => [
                    'plain' => '$',
                    'original' => '\$',
                    'replacement' => ''
                ],

                2 => [
                    'plain' => '\begin{math}',
                    'original' => '\\\begin{math}',
                    'replacement' => ''
                ]

            ],

            'display' => [

                0 => [
                    'plain' => '$$',
                    'original' => '\$\$',
                    'replacement' => ''
                ],

                1 => [
                    'plain' => '\[',
                    'original' => '\\\\\[',
                    'replacement' => ''
                ],

                2 => [
                    'plain' => '\begin{displaymath}',
                    'original' => '\\\\begin{displaymath}',
                    'replacement' => ''
                ],

                3 => [
                    'plain' => '\begin{equation}',
                    'original' => '\\\\begin{equation}',
                    'replacement' => ''
                ]

            ]

        ],

        'parentheses' => [

            'classics' => [

                'bigSm' => [
                    'original' => '\\\big\(',
                    'replacement' => '('
                ],

                'bigLg' => [
                    'original' => '\\\Big\(',
                    'replacement' => '('
                ],

                'biggSm' => [
                    'original' => '\\\bigg\(',
                    'replacement' => '('
                ],

                'biggLg' => [
                    'original' => '\\\Bigg\(',
                    'replacement' => '('
                ]

            ],

            'brackets' => [

                'bigSm' => [
                    'original' => '\\\big\[',
                    'replacement' => '('
                ],

                'bigLg' => [
                    'original' => '\\\Big\[',
                    'replacement' => '('
                ],

                'biggSm' => [
                    'original' => '\\\bigg\[',
                    'replacement' => '('
                ],

                'biggLg' => [
                    'original' => '\\\Bigg\[',
                    'replacement' => '('
                ]

            ],

            'curly' => [

                'bigSm' => [
                    'original' => '\\\big\\\{',
                    'replacement' => '('
                ],

                'bigLg' => [
                    'original' => '\\\Big\\\{',
                    'replacement' => '('
                ],

                'biggSm' => [
                    'original' => '\\\bigg\\\{',
                    'replacement' => '('
                ],

                'biggLg' => [
                    'original' => '\\\Bigg\\\{',
                    'replacement' => '('
                ]

            ],

            'angle' => [

                'bigSm' => [
                    'original' => '\\\big \\\langle',
                    'replacement' => '('
                ],

                'bigLg' => [
                    'original' => '\\\Big \\\langle',
                    'replacement' => '('
                ],

                'biggSm' => [
                    'original' => '\\\bigg \\\langle',
                    'replacement' => '('
                ],

                'biggLg' => [
                    'original' => '\\\Bigg \\\langle',
                    'replacement' => '('
                ]

            ]

        ]

    ];

    protected const SUFFIXES = [

        'global' => [

            'inline' => [

                0 => [
                    'plain' => '\)',
                    'original' => '\\\\\)',
                    'replacement' => ''
                ],

                1 => [
                    'plain' => '$',
                    'original' => '\$',
                    'replacement' => ''
                ],

                2 => [
                    'plain' => '\end{math}',
                    'original' => '\\\end{math}',
                    'replacement' => ''
                ]

            ],

            'display' => [

                0 => [
                    'plain' => '$$',
                    'original' => '\$\$',
                    'replacement' => ''
                ],

                1 => [
                    'plain' => '\]',
                    'original' => '\\\\\]',
                    'replacement' => ''
                ],

                2 => [
                    'plain' => '\end{displaymath}',
                    'original' => '\\\\end{displaymath}',
                    'replacement' => ''
                ],

                3 => [
                    'plain' => '\end{equation}',
                    'original' => '\\\\end{equation}',
                    'replacement' => ''
                ]

            ]

        ],

        'parentheses' => [

            'classics' => [

                'bigSm' => [
                    'original' => '\\\big\)',
                    'replacement' => ')'
                ],

                'bigLg' => [
                    'original' => '\\\Big\)',
                    'replacement' => ')'
                ],

                'biggSm' => [
                    'original' => '\\\bigg\)',
                    'replacement' => ')'
                ],

                'biggLg' => [
                    'original' => '\\\Bigg\)',
                    'replacement' => ')'
                ]

            ],

            'brackets' => [

                'bigSm' => [
                    'original' => '\\\big\]',
                    'replacement' => ')'
                ],

                'bigLg' => [
                    'original' => '\\\Big\]',
                    'replacement' => ')'
                ],

                'biggSm' => [
                    'original' => '\\\bigg\]',
                    'replacement' => ')'
                ],

                'biggLg' => [
                    'original' => '\\\Bigg\]',
                    'replacement' => ')'
                ]

            ],

            'curly' => [

                'bigSm' => [
                    'original' => '\\\big\\\}',
                    'replacement' => ')'
                ],

                'bigLg' => [
                    'original' => '\\\Big\\\}',
                    'replacement' => ')'
                ],

                'biggSm' => [
                    'original' => '\\\bigg\\\}',
                    'replacement' => ')'
                ],

                'biggLg' => [
                    'original' => '\\\Bigg\\\}',
                    'replacement' => ')'
                ]

            ],

            'angle' => [

                'bigSm' => [
                    'original' => '\\\big \\\rangle',
                    'replacement' => ')'
                ],

                'bigLg' => [
                    'original' => '\\\Big \\\rangle',
                    'replacement' => ')'
                ],

                'biggSm' => [
                    'original' => '\\\bigg \\\rangle',
                    'replacement' => ')'
                ],

                'biggLg' => [
                    'original' => '\\\Bigg \\\rangle',
                    'replacement' => ')'
                ]

            ]

        ]

    ];

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * @var RegularExpressions
     */
    protected $regularExpressions;

    /**
     * LatexParser constructor.
     * @param StringsHelper $stringsHelper
     * @param RegularExpressions $regularExpressions
     */
    public function __construct(StringsHelper $stringsHelper, RegularExpressions $regularExpressions)
    {
        $this->stringsHelper = $stringsHelper;
        $this->regularExpressions = $regularExpressions;
    }

    /**
     * @param string $latex
     * @return string
     */
    public static function parseFractions(string $latex): string
    {
        while (Strings::match($latex, '~\\\\frac\{([\da-zA-Z\"\s\<\>\/\=\+\-\*\(\)\^\{\}]*)\}\s*\{([\da-zA-Z\"\s\<\>\/\=\+\-\*\(\)\^\{\}]*)\}~')) {
            $latex = Strings::replace($latex, '~\\\\frac\{([\da-zA-Z\"\s\<\>\/\=\+\-\*\(\)\^\{\}]*)\}\s*\{([\da-zA-Z\"\s\<\>\/\=\+\-\*\(\)\^\{\}]*)\}~', '(($1)/($2))');
        }
        return $latex;
    }

    /**
     * @param string $latex
     * @return string
     */
    public static function parseParentheses(string $latex): string
    {
        $res = $latex;
        foreach (self::PREFIXES[self::PARENTHESES] as $prefixSet) {
            foreach ($prefixSet as $prefix) {
                $res = Strings::replace($res, '~' . $prefix['original'] . '~', $prefix['replacement']);
            }
        }
        foreach (self::SUFFIXES[self::PARENTHESES] as $suffixSet) {
            foreach ($suffixSet as $suffix) {
                $res = Strings::replace($res, '~' . $suffix['original'] . '~', $suffix['replacement']);
            }
        }
        return $res;
    }

    /**
     * @param string $latex
     * @return string
     */
    public static function parseSuperscripts(string $latex): string
    {
        return Strings::replace($latex, '~\^{(.*)}~', '^($1)');
    }

    /**
     * @param string $latex
     * @return string
     */
    public static function parseSubscripts(string $latex): string
    {
        $latex = Strings::replace($latex, '~_{(.*)}~', '$1');
        return Strings::replace($latex, '~(_)~', '');
    }

    /**
     * @param string $latex
     * @return string
     */
    public static function parseLogarithm(string $latex): string
    {
        return Strings::replace($latex, '~(\\\log(\d+|\([\d\+\-\*\/]+\)))~', 'log($2)');
    }

    /**
     * @param string $latex
     * @return string
     */
    public static function trim(string $latex): string
    {
        $res = Strings::trim($latex);
        foreach (self::PREFIXES[self::GLOBAL] as $key1 => $prefixSet) {
            foreach ($prefixSet as $key2 => $prefix) {
                $res = Strings::replace($res, '~' . $prefix['original'] . '~', $prefix['replacement']);
            }
        }
        foreach (self::SUFFIXES[self::GLOBAL] as $key1 => $suffixSet) {
            foreach ($suffixSet as $key2 => $suffix) {
                $res = Strings::replace($res, '~' . $suffix['original'] . '~', $suffix['replacement']);
            }
        }
        return Strings::trim($res);
    }

    /**
     * @param string $latex
     * @return bool
     */
    public static function latexWrapped(string $latex): bool
    {
        $latex = Strings::trim($latex);
        foreach (self::PREFIXES[self::GLOBAL] as $key1 => $prefixSet) {
            foreach ($prefixSet as $key2 => $prefix) {
                /*
                 * Check if the string is wrapped by valid LaTeX mathematical mode wrapper
                 * Check the double occurrence of wrapper
                 */
                if (
                    Strings::startsWith($latex, $prefix['plain']) &&
                    !Strings::startsWith($latex, $prefix['plain'] . $prefix['plain']) &&
                    Strings::endsWith($latex, self::SUFFIXES[self::GLOBAL][$key1][$key2]['plain']) &&
                    !Strings::endsWith($latex, self::SUFFIXES[self::GLOBAL][$key1][$key2]['plain'] . self::SUFFIXES[self::GLOBAL][$key1][$key2]['plain'])
                ) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param string $expression
     * @return string
     */
    public function removeZeroMultipliedBrackets(string $expression): string
    {
        foreach (self::PREFIXES[self::PARENTHESES] as $prefixSetKey => $prefixSet) {
            foreach ($prefixSet as $prefixKey => $prefix) {
                $regex = '~' . sprintf($this->regularExpressions::RE_BRACKETS_ZERO_MULTIPLIED, $prefix['original'], self::SUFFIXES[self::PARENTHESES][$prefixSetKey][$prefixKey]['original']) . '~';
                $expression = Strings::replace($expression, $regex, ' $1 0 ');
            }
        }
        return $expression;
    }

    /**
     * @param string $expression
     * @return string
     */
    public function removeZeroMultipliedFractions(string $expression): string
    {
        return Strings::replace($expression, '~' . $this->regularExpressions::RE_FRACTIONS_ZERO_MULTIPLIED . '~', ' $1 0 ');
    }

    /**
     * @param string $expression
     * @return string
     */
    public function removeZeroMultipliedValues(string $expression): string
    {
        return Strings::replace($expression, '~' . $this->regularExpressions::RE_VALUES_ZERO_MULTIPLIED . '~', ' $1 0 ');
    }

    /**
     * @param string $expression
     * @return string
     */
    public function removeZeroValues(string $expression): string
    {
        $expression = Strings::replace($expression, '~' . $this->regularExpressions::RE_ZERO_VALUES . '~', '');
        foreach (self::PREFIXES[self::GLOBAL] as $prefixSetKey => $prefixSet) {
            foreach ($prefixSet as $prefixKey => $prefix) {
                $regex = '~' . sprintf($this->regularExpressions::RE_STARTING_ZERO_VALUE, $prefix['original']) . '~';
                $expression = Strings::replace($expression, $regex, '$1 $3');
            }
        }
        return $expression;
    }

    /**
     * @param string $body
     * @return string
     */
    public function postprocessFinalBody(string $body): string
    {
        $body = $this->removeZeroMultipliedBrackets($body);
        $body = $this->removeZeroMultipliedFractions($body);
        $body = $this->removeZeroMultipliedValues($body);
        $body = $this->removeZeroValues($body);
        $body = $this->stringsHelper::normalizeOperators($body);
        $body = $this->stringsHelper::deduplicateWhiteSpaces($body);
        $body = Strings::trim($body);
        return $body;
    }

    /**
     * @param string $latex
     * @return string
     */
    public static function parse(string $latex): string
    {
        $res = self::trim($latex);
        $res = self::parseParentheses($res);
        $res = self::parseLogarithm($res);
        $res = self::parseSubscripts($res);
        $res = self::parseSuperscripts($res);
        $res = self::parseFractions($res);
        return $res;
    }
}
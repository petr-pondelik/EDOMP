<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.4.19
 * Time: 19:40
 */

namespace App\Helpers;

use Nette\Utils\Strings;

/**
 * Class LatexHelper
 * @package App\Helpers
 */
class LatexHelper
{
    const GLOBAL = "global";

    const INLINE = "inline";

    const DISPLAY = "display";

    const PARENTHESES = "parentheses";

    const PREFIXES = [

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
                    'original' => '\\\$\\\$',
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

        "parentheses" => [

            "classics" => [

                "bigSm" => [
                    "original" => "\\\big\(",
                    "replacement" => "("
                ],

                "bigLg" => [
                    "original" => "\\\Big\(",
                    "replacement" => "("
                ],

                "biggSm" => [
                    "original" => "\\\bigg\(",
                    "replacement" => "("
                ],

                "biggLg" => [
                    "original" => "\\\Bigg\(",
                    "replacement" => "("
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

                "biggSm" => [
                    "original" => "\\\bigg\[",
                    "replacement" => "("
                ],

                "biggLg" => [
                    "original" => "\\\Bigg\[",
                    "replacement" => "("
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

                "biggSm" => [
                    "original" => "\\\bigg\\\{",
                    "replacement" => "("
                ],

                "biggLg" => [
                    "original" => "\\\Bigg\\\{",
                    "replacement" => "("
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

                "biggSm" => [
                    "original" => "\\\bigg \\\langle",
                    "replacement" => "("
                ],

                "biggLg" => [
                    "original" => "\\\Bigg \\\langle",
                    "replacement" => "("
                ]

            ]

        ]

    ];

    const SUFFIXES = [

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
                    'original' => '\\$\\$',
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
     * @param string $latex
     * @return string
     */
    static public function parseFractions(string $latex): string
    {
        return Strings::replace($latex, '~(\\\frac\{([^\\\]*)\}\{([^\\\]*)\})~', '($2)/($3)');
    }

    /**
     * @param string $latex
     * @return string
     */
    static public function parseParentheses(string $latex): string
    {
        $res = $latex;
        foreach (self::PREFIXES[self::PARENTHESES] as $prefixSet){
            foreach ($prefixSet as $prefix)
                $res = Strings::replace($res, '~' . $prefix["original"] . '~', $prefix["replacement"]);
        }
        foreach (self::SUFFIXES[self::PARENTHESES] as $suffixSet){
            foreach ($suffixSet as $suffix)
                $res = Strings::replace($res, '~' . $suffix["original"] . '~', $suffix["replacement"]);
        }
        return $res;
    }

    /**
     * @param string $latex
     * @return string
     */
    static public function parseExponent(string $latex): string
    {
        return Strings::replace($latex, '~\^{(.*)}~', '^($1)');
    }

    /**
     * @param string $latex
     * @return string
     */
    static public function parseSubscription(string $latex): string
    {
        return Strings::replace($latex, "~(_)~", "");
    }

    /**
     * @param string $latex
     * @return string
     */
    static public function trim(string $latex): string
    {
        $res = $latex;
        foreach (self::PREFIXES[self::GLOBAL] as $key1 => $prefixSet){
            foreach ($prefixSet as $key2 => $prefix)
                $res = Strings::replace($res, '~' . $prefix["original"] . '~', $prefix["replacement"]);
        }
        foreach (self::SUFFIXES[self::GLOBAL] as $key1 => $suffixSet){
            foreach ($suffixSet as $key2 => $suffix)
                $res = Strings::replace($res, '~' . $suffix["original"] . '~', $suffix["replacement"]);
        }
        bdump(Strings::trim($res));
        return Strings::trim($res);
    }

    /**
     * @param string $latex
     * @return string
     */
    static public function parseLatex(string $latex): string
    {
        $res = self::trim($latex);
        $res = self::parseParentheses($res);
        $res = self::parseFractions($res);
        $res = self::parseExponent($res);
        $res = self::parseSubscription($res);
        return $res;
    }

    /**
     * @param string $latex
     * @return bool
     */
    static public function latexWrapped(string $latex): bool
    {
        foreach(self::PREFIXES[self::GLOBAL] as $key1 => $prefixSet){
            foreach ($prefixSet as $key2 => $prefix){
                if(Strings::startsWith($latex, $prefix["plain"]) && Strings::endsWith($latex, self::SUFFIXES[self::GLOBAL][$key1][$key2]["plain"]))
                    return true;
            }
        }
        return false;
    }
}
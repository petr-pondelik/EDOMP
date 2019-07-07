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
    public static function parseFractions(string $latex): string
    {
        while(Strings::match($latex, '~\\\\frac\{([0-9a-zA-Z" <>\/\=\+\-\*\(\)\^\{\}]*)\}\{([0-9a-zA-Z" <>\/\=\+\-\*\(\)\^\{\}]*)\}~')){
            $latex = Strings::replace($latex, '~\\\\frac\{([0-9a-zA-Z" <>\/\=\+\-\*\(\)\^\{\}]*)\}\{([0-9a-zA-Z" <>\/\=\+\-\*\(\)\^\{\}]*)\}~', '($1)/($2)');
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
        foreach (self::PREFIXES[self::PARENTHESES] as $prefixSet){
            foreach ($prefixSet as $prefix){
                $res = Strings::replace($res, '~' . $prefix['original'] . '~', $prefix['replacement']);
            }
        }
        foreach (self::SUFFIXES[self::PARENTHESES] as $suffixSet){
            foreach ($suffixSet as $suffix){
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
    public static function trim(string $latex): string
    {
        $res = $latex;
        foreach (self::PREFIXES[self::GLOBAL] as $key1 => $prefixSet){
            foreach ($prefixSet as $key2 => $prefix){
                $res = Strings::replace($res, '~' . $prefix['original'] . '~', $prefix['replacement']);}
        }
        foreach (self::SUFFIXES[self::GLOBAL] as $key1 => $suffixSet){
            foreach ($suffixSet as $key2 => $suffix) {
                $res = Strings::replace($res, '~' . $suffix['original'] . '~', $suffix['replacement']);
            }
        }
        return Strings::trim($res);
    }

    /**
     * @param string $latex
     * @return string
     */
    public static function parseLatex(string $latex): string
    {
        $res = self::trim($latex);
        $res = self::parseParentheses($res);
        $res = self::parseFractions($res);
        $res = self::parseSubscripts($res);
        $res = self::parseSuperscripts($res);
        bdump($res);
        return $res;
    }

    /**
     * @param string $latex
     * @return bool
     */
    public static function latexWrapped(string $latex): bool
    {
        foreach(self::PREFIXES[self::GLOBAL] as $key1 => $prefixSet){
            foreach ($prefixSet as $key2 => $prefix){
                if(Strings::startsWith($latex, $prefix['plain']) && Strings::endsWith($latex, self::SUFFIXES[self::GLOBAL][$key1][$key2]['plain'])) {
                    return true;
                }
            }
        }
        return false;
    }
}
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

    const PARENTHESES = "parentheses";

    const PREFIXES = [

        "global" => [
            "inline" => [
                "plain" => "\(",
                "original" => "\\\\\(",
                "replacement" => ""
            ],
            "center" => [
                "plain" => "$$",
                "original" => "\\$\\$",
                "replacement" => ""
            ]
        ],

        "parentheses" => [

            "parenthesesBigSm" => [
                "original" => "\\\big\(",
                "replacement" => "("
            ],

            "parenthesesBigLg" => [
                "original" => "\\\Big\(",
                "replacement" => "("
            ],

            "parenthesesBiggSm" => [
                "original" => "\\\bigg\(",
                "replacement" => "("
            ],

            "parenthesesBiggLg" => [
                "original" => "\\\Bigg\(",
                "replacement" => "("
            ]
        ]

    ];

    const SUFFIXES = [

        "global" => [
            "inline" => [
                "plain" => "\)",
                "original" => "\\\\\)",
                "replacement" => ""
            ],
            "center" => [
                "plain" => "$$",
                "original" => "\\$\\$",
                "replacement" => ""
            ]
        ],

        "parentheses" => [

            "parenthesesBigSm" => [
                "original" => "\\\big\)",
                "replacement" => ")"
            ],

            "parenthesesBigLg" => [
                "original" => "\\\Big\)",
                "replacement" => ")"
            ],

            "parenthesesBiggSm" => [
                "original" => "\\\bigg\)",
                "replacement" => ")"
            ],

            "parenthesesBiggLg" => [
                "original" => "\\\Bigg\)",
                "replacement" => ")"
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
    static public function parseParentheses(string $latex) :string
    {
        $res = $latex;
        foreach (self::PREFIXES[self::PARENTHESES] as $prefixKey => $prefix)
            $res = Strings::replace($res, '~' . $prefix["original"] . '~', $prefix["replacement"]);
        foreach (self::SUFFIXES[self::PARENTHESES] as $suffixKey => $suffix)
            $res = Strings::replace($res, '~' . $suffix["original"] . '~', $suffix["replacement"]);
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
        foreach (self::PREFIXES[self::GLOBAL] as $prefixKey => $prefix)
            $res = Strings::replace($res, '~' . $prefix["original"] . '~', $prefix["replacement"]);
        foreach (self::SUFFIXES[self::GLOBAL] as $suffixKey => $suffix)
            $res = Strings::replace($res, '~' . $suffix["original"] . '~', $suffix["replacement"]);
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
        bdump($res);
        return $res;
    }

    /**
     * @param string $latex
     * @return bool
     */
    static public function latexWrapped(string $latex): bool
    {
        foreach(self::PREFIXES[self::GLOBAL] as $key => $prefix){
            if(Strings::startsWith($latex, $prefix["plain"]) && Strings::endsWith($latex, self::SUFFIXES[self::GLOBAL][$key]["plain"]))
                return true;
        }
        return false;
    }
}
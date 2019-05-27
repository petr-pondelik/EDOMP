<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.3.19
 * Time: 18:39
 */

namespace App\Helpers;

use App\Exceptions\StringFormatException;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class StringsHelper
 * @package app\helpers
 */
class StringsHelper
{
    const LATEX_INLINE = "latexInline";
    const BRACKETS_SIMPLE = "bracketsSimple";
    const ADDITION = "addition";
    const SUBTRACTION = "subtraction";

    const IS_ADDITION = 1;
    CONST IS_SUBTRACTION = 2;

    const PREFIXES = [
        "latexInline" => "\\(",
        "bracketsSimple" => "(",
        "addition" => "+",
        "subtraction" => "-"
    ];

    const SUFFIXES = [
        "latexInline" => "\\)",
        "bracketsSimple" => ")",
        "addition" => "+",
        "subtraction" => "-"
    ];

    /**
     * @param string $expression
     * @return array
     */
    static public function splitByParameters(string $expression)
    {
        //Explode string by parameter marks and preserve the marks

        /*SPLIT REGULAR EXPRESSION:
            ( <par\s*\/> ) |
            ( <par\s*type="[a-z]*"\s*\/> ) |
            ( <par\s*type="[a-z]*"\s*min="[0-9]*"\s*\/> ) |
            ( <par\s*type="[a-z]*"\s*max="[0-9]*"\/> ) |
            ( <par\s*type="[a-z]*"\s*min="[0-9]*"\s*max="[0-9]*"\/> ) |
            ( <par\s*min="[0-9]*"\s*\/> ) |
            ( <par\s*max="[0-9]*"\s*\/> ) |
            ( <par\s*min="[0-9]*"\s*max="[0-9]*"\s*\/> )
        */

        return Strings::split($expression,'~(<par\s*\/>)|(<par\s*type="[a-z]*"\s*\/>)|(<par\s*type="[a-z]*"\s*min="[0-9]*"\s*\/>)|(<par\s*type="[a-z]*"\s*max="[0-9]*"\/>)|(<par\s*type="[a-z]*"\s*min="[0-9]*"\s*max="[0-9]*"\/>)|(<par\s*min="[0-9]*"\s*\/>)|(<par\s*max="[0-9]*"\s*\/>)|(<par\s*min="[0-9]*"\s*max="[0-9]*"\s*\/>)~');
    }

    /**
     * @param string $expression
     * @return string
     */
    static public function removeWhiteSpaces(string $expression)
    {
        return Strings::replace($expression, "~\s~", "");
    }

    /**
     * @param string $expression
     * @return string
     */
    static public function negateOperators(string $expression)
    {
        for($i = 0; $i < strlen($expression); $i++){
            if($expression[$i] == "+"){
                $expression[$i] = "-";
                continue;
            }
            if($expression[$i] == "-") $expression[$i] = "+";
        }
        return $expression;
    }

    /**
     * @param string $expression
     * @return int
     */
    static public function firstOperator(string $expression)
    {
        $addInx = Strings::indexOf($expression, '+', 1);
        $subInx = Strings::indexOf($expression, '-', 1);
        if(!$addInx) return self::IS_SUBTRACTION;
        if(!$subInx) return self::IS_ADDITION;
        return $addInx < $subInx ? self::IS_ADDITION : self::IS_SUBTRACTION;
    }

    /**
     * @param string $expression
     * @return false|string
     */
    static public function trimOperators(string $expression)
    {
        $expression = self::trim($expression, self::ADDITION);
        $expression = self::trim($expression, self::SUBTRACTION);
        return $expression;
    }

    /**
     * @param string $expression
     * @param string $type
     * @return false|string
     */
    static public function trim(string $expression, $type = self::BRACKETS_SIMPLE)
    {
        $res = Strings::trim($expression);
        if(Strings::startsWith($res, self::PREFIXES[$type]))
            $res = Strings::after($res, self::PREFIXES[$type], 1);
        if(Strings::endsWith($res, self::SUFFIXES[$type]))
            $res = Strings::before($res, self::SUFFIXES[$type], -1);
        return Strings::trim($res);
    }

    /**
     * @param string $expression
     * @param string $wrapper
     * @return string
     */
    static public function wrap(string $expression, $wrapper = self::BRACKETS_SIMPLE)
    {
        return self::PREFIXES[$wrapper] . $expression . self::SUFFIXES[$wrapper];
    }

    /**
     * @param string $expression
     * @return string
     */
    static public function newtonFormat(string $expression): string
    {
        $expression = self::newtonFractions($expression);
        return $expression;
    }

    /**
     * @param string $expression
     * @return string
     */
    static public function newtonFractions(string $expression): string
    {
        return Strings::replace($expression, "~(\/)~", '(over)');
    }

    /**
     * @param string $expression
     * @param string|null $variable
     * @return string
     */
    static public function nxpFormat(string $expression, string $variable = null): string
    {
        $expression = Strings::replace($expression, '~(\d)(\s)(p)~', '$1*$3');
        $expression = Strings::replace($expression, '~(\d)(\s)(\d)~', '$1*$3');
        if($variable !== null){
            $expression = Strings::replace($expression, '~(\d)(\s)(' . $variable . ')~', '$1*$3');
            $expression = Strings::replace($expression, '~(' . $variable . ')(\s)(\d)~', '$1*$3');
        }
        return $expression;
    }

    /**
     * @param string $expression
     * @return string
     */
    static public function extractSequenceName(string $expression): string
    {
        return (Strings::match($expression, "~^\s*(\w*)\w$~"))[1];
    }

    /**
     * @param String $xmpPar
     * @param String $attr
     * @return int
     */
    static public function extractParAttr(String $xmpPar, String $attr)
    {
        $start = Strings::indexOf($xmpPar, $attr);
        if(!$start)
            return null;
        $xmpPar = Strings::substring($xmpPar, $start);
        $end = Strings::indexOf($xmpPar, '"', 2);
        return (int) Strings::substring($xmpPar, Strings::indexOf($xmpPar, '"') + 1, $end - Strings::indexOf($xmpPar, '"') - 1);
    }

    /**
     * @param string $expression
     * @return int
     */
    static public function extractParamsCnt(string $expression)
    {
        return substr_count($expression, "<par");
    }

    /**
     * @param string $expression
     * @return ArrayHash
     */
    static public function extractParametersInfo(string $expression)
    {
        $expressionSplit = self::splitByParameters($expression);
        $parametersMinMax = [];
        $parametersComplexity = 1;
        $parametersCnt = 0;
        foreach ($expressionSplit as $item){
            if(Strings::contains($item, '<par')){
                $min = self::extractParAttr($item, "min");
                $max = self::extractParAttr($item, "max");
                $parametersMinMax[$parametersCnt++] = [
                    "min" => $min,
                    "max" => $max
                ];
                $parametersComplexity *= (($max - $min) + 1);
            }
        }
        return ArrayHash::from([
            "count" => $parametersCnt,
            "complexity" => $parametersComplexity,
            "minMax" => $parametersMinMax
        ]);
    }

    /**
     * @param string $expression
     * @param bool $validate
     * @return ArrayHash
     */
    static public function getEquationSides(string $expression, bool $validate = true)
    {
        if($validate){
            if(!self::isEquation($expression))
                throw new StringFormatException('Zadaný výraz není rovnicí.');
        }
        $sides = Strings::split($expression, '~=~');
        return ArrayHash::from([
            "left" => Strings::trim($sides[0]),
            "right" => Strings::trim($sides[1])
        ]);
    }

    /**
     * @param ArrayHash $sides
     * @return string
     */
    static public function mergeEqSides(ArrayHash $sides)
    {
        if($sides->left === "0")
            return $sides->left;
        return $sides->left . " - (" . $sides->right . ")";
    }

    /**
     * @param string $expression
     * @param iterable $values
     * @return string
     */
    static public function passValues(string $expression, iterable $values)
    {
        foreach($values as $parameter => $value)
            $expression = Strings::replace($expression, '~' . $parameter . '~', $value);
        return $expression;
    }

    /**
     * @param string $expression
     * @return ArrayHash
     */
    static public function getParametrized(string $expression)
    {
        $expressionSplit = self::splitByParameters($expression);
        $parametrized = [];
        $parametersCnt = 0;
        foreach ($expressionSplit as $splitKey => $splitItem){
            if($splitItem !== ""){
                if(!Strings::match($splitItem, '~(<par.*\/>)~')) $parametrized[$splitKey] = $splitItem;
                else $parametrized[$splitKey] = 'p'.$parametersCnt++;
            }
        }
        return ArrayHash::from([
            "expression" => join($parametrized),
            "parametersCnt" => $parametersCnt
        ]);
    }

    /**
     * @param string $expression
     * @param string $variable
     * @return string
     */
    static public function getLinearVariableExpresion(string $expression, string $variable): string
    {
        $split = Strings::split($expression, '~(' . $variable . ')~');
        if(!$split[2]) return "0";
        $rightOp = "";
        if(self::firstOperator($split[2]) === self::IS_ADDITION) $rightOp = "-";
        $split[2] = self::trimOperators($split[2]);
        $split[2] = self::negateOperators($split[2]);

        //Check if variable multiplier exists
        if($split[0]) $rightSide = '(' . $rightOp . $split[2] .')' . ' / ' . $split[0];
        else $rightSide = '(' . $rightOp . $split[2] .')';

        $rightSide = self::nxpFormat($rightSide);
        return $rightSide;
    }

    /**
     * @param array $split
     * @param string $variable
     * @return bool
     */
    static public function containsVariable(array $split, string $variable): bool
    {
        foreach($split as $part){
            if($part !== "" && !Strings::startsWith($part, "<par")){
                if(Strings::contains($part, $variable))
                    return true;
            }
        }
        return false;
    }

    /**
     * @param string $expression
     * @return bool
     */
    static public function isEquation(string $expression): bool
    {
        $split = Strings::split($expression, "~ = ~");
        if(count($split) !== 2 || Strings::match($split[0], "~^\s*$~") || Strings::match($split[1], "~^\s*$~"))
            return false;
        if(Strings::match($expression, "~^\w\w\s*=~"))
            return false;
        return true;
    }

    /**
     * @param string $expression
     * @return bool
     */
    static public function isSequence(string $expression): bool
    {
        $split = Strings::split($expression, "~ = ~");
        if(count($split) !== 2 || Strings::match($split[0], "~^\s*$~") || Strings::match($split[1], "~^\s*$~"))
            return false;
        if(!Strings::match($expression, "~^\w\w\s*=~"))
            return false;
        return true;
    }
}
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
     * @param bool $validation
     * @return array
     */
    public static function splitByParameters(string $expression, bool $validation = false): array
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

        if(!$validation){
            return Strings::split($expression,'~(<par\s*min="[0-9]+"\s*max="[0-9]+"\s*\/>)~');
        }

        return Strings::split($expression,'~(<par\s*\/>)|(<par\s*type="[a-z]*"\s*\/>)|(<par\s*type="[a-z]*"\s*min="[0-9]*"\s*\/>)|(<par\s*type="[a-z]*"\s*max="[0-9]*"\/>)|(<par\s*type="[a-z]*"\s*min="[0-9]*"\s*max="[0-9]*"\/>)|(<par\s*min="[0-9]*"\s*\/>)|(<par\s*max="[0-9]*"\s*\/>)|(<par\s*min="[0-9]*"\s*max="[0-9]*"\s*\/>)~');
    }

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
    public static function negateOperators(string $expression): string
    {
        $expessionLen = strlen($expression);
        for($i = 0; $i < $expessionLen; $i++){
            if($expression[$i] === '+'){
                $expression[$i] = '-';
                continue;
            }
            if($expression[$i] === '-'){
                $expression[$i] = '+';
            }
        }
        return $expression;
    }

    /**
     * @param string $expression
     * @return int
     */
    public static function firstOperator(string $expression): int
    {
        $addInx = Strings::indexOf($expression, '+');
        $subInx = Strings::indexOf($expression, '-');
        if(!$addInx){
            return self::IS_SUBTRACTION;
        }
        if(!$subInx){
            return self::IS_ADDITION;
        }
        return $addInx < $subInx ? self::IS_ADDITION : self::IS_SUBTRACTION;
    }

    /**
     * @param string $expression
     * @return string
     */
    public static function trimOperators(string $expression): string
    {
        $expression = self::trim($expression, self::ADDITION);
        $expression = self::trim($expression, self::SUBTRACTION);
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
        if(Strings::startsWith($res, self::PREFIXES[$type])){
            $res = Strings::after($res, self::PREFIXES[$type]);
        }
        if(Strings::endsWith($res, self::SUFFIXES[$type])){
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
     * @param String $xmpPar
     * @param String $attr
     * @return int
     */
    public static function extractParAttr(String $xmpPar, String $attr): int
    {
        $start = Strings::indexOf($xmpPar, $attr);
        if(!$start){
            return null;
        }
        $xmpPar = Strings::substring($xmpPar, $start);
        $end = Strings::indexOf($xmpPar, '"', 2);
        return (int) Strings::substring($xmpPar, Strings::indexOf($xmpPar, '"') + 1, $end - Strings::indexOf($xmpPar, '"') - 1);
    }

    /**
     * @param string $expression
     * @return int
     */
    public static function extractParamsCnt(string $expression): int
    {
        return substr_count($expression, '<par');
    }

    /**
     * @param string $expression
     * @return ArrayHash
     */
    public static function extractParametersInfo(string $expression): ArrayHash
    {
        $expressionSplit = self::splitByParameters($expression);
        $parametersMinMax = [];
        $parametersComplexity = 1;
        $parametersCnt = 0;
        foreach ($expressionSplit as $item){
            if(Strings::contains($item, '<par')){
                $min = self::extractParAttr($item, 'min');
                $max = self::extractParAttr($item, 'max');
                $parametersMinMax[$parametersCnt++] = [
                    'min' => $min,
                    'max' => $max
                ];
                $parametersComplexity *= (($max - $min) + 1);
            }
        }
        return ArrayHash::from([
            'count' => $parametersCnt,
            'complexity' => $parametersComplexity,
            'minMax' => $parametersMinMax
        ]);
    }

    /**
     * @param string $expression
     * @param bool $validate
     * @return ArrayHash
     */
    public static function getEquationSides(string $expression, bool $validate = true): ArrayHash
    {
        if($validate && !self::isEquation($expression)){
                throw new StringFormatException('Zadaný výraz není rovnicí.');
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
        if($sides->left === '0'){
            return $sides->left;
        }
        return $sides->left . ' - (' . $sides->right . ')';
    }

    /**
     * @param string $expression
     * @param iterable $values
     * @return string
     */
    public static function passValues(string $expression, iterable $values): string
    {
        foreach($values as $parameter => $value){
            $expression = Strings::replace($expression, '~' . $parameter . '~', $value);
        }
        return $expression;
    }

    /**
     * @param string $expression
     * @return ArrayHash
     */
    public static function getParametrized(string $expression): ArrayHash
    {
        $expressionSplit = self::splitByParameters($expression);
        $parametrized = [];
        $parametersCnt = 0;
        foreach ($expressionSplit as $splitKey => $splitItem){
            if($splitItem !== ''){
                if(!Strings::match($splitItem, '~(<par.*\/>)~')){
                    $parametrized[$splitKey] = $splitItem;
                }
                else{
                    $parametrized[$splitKey] = 'p' . $parametersCnt++;
                }
            }
        }
        return ArrayHash::from([
            'expression' => join($parametrized),
            'parametersCnt' => $parametersCnt
        ]);
    }

    /**
     * @param string $expression
     * @param string $variable
     * @return string
     */
    public static function getLinearVariableExpresion(string $expression, string $variable): string
    {
        $split = Strings::split($expression, '~(' . $variable . ')~');
        if(!$split[2]){
            return '0';
        }
        $rightOp = '';
        if(self::firstOperator($split[2]) === self::IS_ADDITION){
            $rightOp = '-';
        }
        $split[2] = self::trimOperators($split[2]);
        $split[2] = self::negateOperators($split[2]);

        // Check if variable multiplier exists
        if($split[0]){
            $rightSide = sprintf('(%s%s)/(%s)', $rightOp, $split[2], $split[0]);
        }
        else{
            $rightSide =  sprintf('(%s%s)', $rightOp, $split[2]);
        }

        return $rightSide;
    }

    /**
     * @param array $split
     * @param string $variable
     * @return bool
     */
    public static function containsVariable(array $split, string $variable): bool
    {
        foreach($split as $part){
            if($part !== '' && !Strings::startsWith($part, '<par')){
                if(Strings::contains($part, $variable)){
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
        $split = Strings::split($expression, '~ = ~');
        if(count($split) !== 2 || Strings::match($split[0], '~^\s*$~') || Strings::match($split[1], '~^\s*$~')){
            return false;
        }
        if(Strings::match($expression, '~^\s+=\s+~')){
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
        bdump('IS SEQUENCE');
        bdump($expression);
        if(!Strings::match($expression, '~^\s*\w' . $variable . '\s*=~')){
            return false;
        }
        return true;
    }

    /**
     * @param string $expression
     * @param string $variable
     * @return string
     */
    public static function fillMultipliers(string $expression, string $variable): string
    {
        $expression = Strings::replace($expression, '~(\d)(' . $variable . ')~', '$1*$2');
        $expression = Strings::replace($expression, '~(\d)(' . $variable . ')~', '$1*$2');
        $expression = Strings::replace($expression, '~(\d)\s*(' . $variable . ')~', '$1*$2');
        $expression = Strings::replace($expression, '~(\d)\s*(p\d+)~', '$1*$2');
        $expression = Strings::replace($expression, '~(\))\s*(p\d+)~', '$1*$2');
        $expression = Strings::replace($expression, '~(\d)\s+(\d)~', '$1*$2');
        return Strings::replace($expression, '~(\s*\))(' . $variable . ')~', '$1*$2');
    }
}
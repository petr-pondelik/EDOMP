<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 22.11.19
 * Time: 15:58
 */

namespace App\TeacherModule\Services;

use App\CoreModule\Helpers\StringsHelper;
use App\CoreModule\Interfaces\IParser;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class ParameterParser
 * @package App\TeacherModule\Services
 */
final class ParameterParser implements IParser
{
    protected const PARAMETER_ATTR_KEY_REG = '\w*';
    protected const PARAMETER_ATTR_VALUE_REG = '\-?[0-9a-zA-Z\*\-\+\/\^\"\'\>\<]*';

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * ParameterParser constructor.
     * @param StringsHelper $stringsHelper
     */
    public function __construct(StringsHelper $stringsHelper)
    {
        $this->stringsHelper = $stringsHelper;
    }

    /**
     * @param string $expression
     * @return array
     */
    public static function splitByParameters(string $expression): array
    {
        // Explode string by parameter marks and preserve the marks
        /*
         * SPLIT REGULAR EXPRESSION:
         * ( <par\s*min="\-[0-9]+"\s*max="\-[0-9]+"\s*\/> )
         * */
        return Strings::split($expression, '~(<par\s*min="\-?\d+"\s*max="\-?\d+"\s*\/>)~');
    }

    /**
     * @param string $par
     * @param string $attr
     * @return int
     */
    public static function extractParAttr(string $par, string $attr): int
    {
        $start = Strings::indexOf($par, $attr);
        if (!$start) {
            return null;
        }
        $par = Strings::substring($par, $start);
        $end = Strings::indexOf($par, '"', 2);
        return (int)Strings::substring($par, Strings::indexOf($par, '"') + 1, $end - Strings::indexOf($par, '"') - 1);
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
        foreach ($expressionSplit as $item) {
            if (Strings::contains($item, '<par')) {
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
     * @return array
     */
    public static function findParametersAll(string $expression): array
    {
        $regExp = '~'
            . '(<par\s*/?>)|'
            . sprintf("(<par\s*min=?[\"']*%s[\"']*\s*/>)|", self::PARAMETER_ATTR_VALUE_REG)
            . sprintf("(<par\s*max=?[\"']*%s[\"']*\s*/>)|", self::PARAMETER_ATTR_VALUE_REG)
            . sprintf("(<par\s*%s=?[\"']*%s[\"']*\s*%s=?[\"']*%s[\"']*\s*[\/]?[>]?)", self::PARAMETER_ATTR_KEY_REG, self::PARAMETER_ATTR_VALUE_REG, self::PARAMETER_ATTR_KEY_REG, self::PARAMETER_ATTR_VALUE_REG)
            . '~';

        return Strings::split($expression, $regExp);
    }

    /**
     * @param string $expression
     * @param iterable $values
     * @return string
     */
    public function passValues(string $expression, iterable $values): string
    {
        foreach ($values as $parameter => $value) {
            $expression = Strings::replace($expression, '~' . $parameter . '~', $value);
        }
        return $this->stringsHelper::normalizeOperators($expression);
    }

    /**
     * @param string $input
     * @return ArrayHash
     */
    public static function parse(string $input): ArrayHash
    {
        bdump('PARAMETRIZE');
        $expressionSplit = self::splitByParameters($input);
        $parametrized = [];
        $parametersCnt = 0;

        foreach ($expressionSplit as $splitKey => $splitItem) {
            if ($splitItem !== '') {
                if (!Strings::match($splitItem, '~(<par.*\/>)~')) {
                    $parametrized[$splitKey] = $splitItem;
                } else {
                    $parametrized[$splitKey] = 'p' . $parametersCnt++;
                }
            }
        }

        // Merge exploded expression
        $parametrized = implode($parametrized);

        // Fill spaces around parameters
        $parametrized = Strings::replace($parametrized, '~(p\d)+~', ' $1 ');

        return ArrayHash::from([
            'expression' => $parametrized,
            'parametersCnt' => $parametersCnt
        ]);
    }
}
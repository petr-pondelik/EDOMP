<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.9.19
 * Time: 17:57
 */

namespace App\Helpers;

/**
 * Class RegularExpressions
 * @package App\Helpers
 */
class RegularExpressions
{
    // Match operator or whitespace
    public const RE_OPERATOR_WS = '(\+|\-|)';

    // Match parameter
    public const RE_PARAMETER = '(p(\d)+)';

    // Match logarithm
    public const RE_LOGARITHM = '(log\d+|log\([\d\+\-\*\/]+\))';

    // Match number, parameter or fraction with numbers and parameters
    public const RE_NUM_PAR_FRAC = '([\dp\+\-\*\(\)]+\/[\dp\+\-\*\(\)]+|[\dp\+\-\*\(\)]+|)';

    // Match symbols allowed in standardized equation
    public const RE_EQUATION_SYMBOLS = '[\dp\+\-\*\(\)\/\^]';

    // Match parameter's attribute key
    public const RE_PARAMETER_ATTR_KEY_REG = '\w*';

    // Match parameter's attribute value
    public const RE_PARAMETER_ATTR_VALUE_REG = '\-?[0-9a-zA-Z\*\-\+\/\^\"\'\>\<]*';

    // Match parameter opening tag
    public const RE_PARAMETER_OPENING = '<par';

    // Match valid form of parameter
    public const RE_PARAMETER_VALID = '<par\smin=\"\-?\d+\"\smax=\"\-?\d+\"/>';

    // Match standardized variable fraction
    public const RE_VARIABLE_STANDARDIZED_FRACTION = '~' . '(\+?\-?[p%s\d\s\^]+)' . '\/\s*' . '(\([p%s\-\+\^\s\(\)\d]+\)|%s)' . '~';

    // Match variable coefficients by exponent
    public const RE_VARIABLE_COEFFICIENT = '(\(?[\dp\+\-\*\(\)\/\s]*)\)*%s\^?(\d*)';

    // Match variable coefficients without linear variable coefficient
    public const RE_VARIABLE_COEFFICIENT_NON_LINEAR = '(\(?[\dp\+\-\*\(\)\/\s]*)\)*%s\^(\d+)';

    // Split expression by it's first operator
    public const RE_FIRST_OPERATOR_SPLIT = '^([\s\d\/p]*)(\+|\-)(.*)$';

    // Match discriminant A coefficient
    public const RE_DISCRIMINANT_A_COEFFICIENT = '(\([\dp\s\+\-\*\/]+\)|\+?\-?[\s\dp\/]*)\s*%s\^2\s*\/?([\s\dp]*)';

    // Match discriminant B coefficient
    public const RE_DISCRIMINANT_B_COEFFICIENT = '(\([\dp\s\+\-\*\/]+\)|\+?\-?[\s\dp\/]*)\s*%s\s*\/?([\s\dp]*)';

    // Match discriminant C coefficient
    public const RE_DISCRIMINANT_C_COEFFICIENT = '^(\s*\/\s*[p\d]+)?(.*)';

    // Match string to be only number or fraction
    public const RE_NUM_FRAC = '^[\d\/]+$';

    // Match school year format
    public const RE_SCHOOL_YEAR = '[0-9]{4}(\/|\-)([0-9]{4}|[0-9]{2})';

    // Match brackets with passed prefix and suffix multiplied by zero
    public const RE_BRACKETS_ZERO_MULTIPLIED = '[\+\-]?\s*0\s*%s[\d\\a-zA-Z\"\s\<\>\/\=\+\-\*\(\)\^\{\}]*%s';

    // Match fractions multiplied by zero
    public const RE_FRACTIONS_ZERO_MULTIPLIED = '[\+\-]?\s*0\s*\\\frac\{[\d\\a-zA-Z\"\s\<\>\/\=\+\-\*\(\)\^\{\}]*\}\s*\{[\da-zA-Z\"\s\<\>\/\=\+\-\*\(\)\^\{\}]*\}';

    // Match values multiplied by zero
    public const RE_VALUES_ZERO_MULTIPLIED = '[\+\-]\s*0(\s+|\s*\*\s*)\d';

    // Match zero values
    public const RE_ZERO_VALUES = '[\+\-]\s*0\s*';

    // Match test template content
    public const RE_TEST_TEMPLATE = '.*\{if\$test->getLogo\(\)!==null\}.*\{\$test->getSchoolYear\(\)\}.*\{\$test->getTerm\(\)\}.*\{\$test->getTestNumber\(\)\}.*\{\$testVariant\}.*\{foreach\$test->getGroups\(\)->getValues\(\)as\$group\}.*\{\$group->getLabel\(\)\}\{if\$group->getId\(\)!==\$test->getGroups\(\)->last\(\)->getId\(\)\}.*\{\/if\}\{\/foreach\}.*\{\$test->getIntroductionText\(\)\}.*\{foreach\$testVariant->getProblemFinalAssociations\(\)->getValues\(\)as\$key=>\$problemFinalAssociation\}.*\{\$problemFinalAssociation->getProblemFinal\(\)->getTextBefore\(\)\}\{\$problemFinalAssociation->getProblemFinal\(\)->getBody\(\)\}\{\$problemFinalAssociation->getProblemFinal\(\)->getTextAfter\(\)\}\{if\$problemFinalAssociation->isNextPage\(\)\}.*\{\/if}\{\/foreach\}.*';

    /**
     * @param string $variable
     * @return string
     */
    public static function getSequenceRE(string $variable): string
    {
        return '^\s*\w'
            . $variable
            . '\s*=';
    }

    /**
     * @param string $variable
     * @return string
     */
    public static function getLinearEquationRE(string $variable): string
    {
        // Enable all basic equation symbols, logarithm sequence and equation variable
        return '('
            . self::RE_EQUATION_SYMBOLS
            . '|'
            . self::RE_LOGARITHM
            . ')*'
            . $variable
            . '('
            . self::RE_EQUATION_SYMBOLS
            . '|'
            . self::RE_LOGARITHM
            . ')*';
    }

    /**
     * @param string $variable
     * @return mixed
     */
    public static function getQuadraticEquationRE(string $variable): string
    {
        return '('
            . '('
            . self::RE_EQUATION_SYMBOLS
            . '|'
            . self::RE_LOGARITHM
            . ')*'
            . $variable . '\^\d'
            . ')*'
            . '('
            . self::RE_EQUATION_SYMBOLS
            . '|'
            . self::RE_LOGARITHM
            . ')*'
            . $variable . '\^2'
            . '('
            . '([\dp' . $variable . '\+\-\*\(\)\/\^])'
            . '|'
            . self::RE_LOGARITHM
            . ')*';
    }
}
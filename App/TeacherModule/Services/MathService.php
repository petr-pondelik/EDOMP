<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 18.8.19
 * Time: 18:57
 */

namespace App\TeacherModule\Services;

use App\CoreModule\Helpers\RegularExpressions;
use App\CoreModule\Helpers\StringsHelper;
use App\TeacherModule\Exceptions\EquationException;
use App\TeacherModule\Model\NonPersistent\Entity\EquationTemplateNP;
use jlawrence\eos\Parser;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class MathService
 * @package App\TeacherModule\Services
 */
final class MathService
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var ProblemGenerator
     */
    protected $generatorService;

    /**
     * @var ParameterParser
     */
    protected $parameterParser;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * @var VariableFractionService
     */
    protected $variableFractionService;

    /**
     * @var RegularExpressions
     */
    protected $regularExpressions;

    /**
     * MathService constructor.
     * @param Parser $parser
     * @param ProblemGenerator $generatorService
     * @param ParameterParser $parameterParser
     * @param StringsHelper $stringsHelper
     * @param VariableFractionService $variableFractionService
     * @param RegularExpressions $regularExpressions
     */
    public function __construct
    (
        Parser $parser,
        ProblemGenerator $generatorService,
        ParameterParser $parameterParser,
        StringsHelper $stringsHelper,
        VariableFractionService $variableFractionService,
        RegularExpressions $regularExpressions
    )
    {
        $this->parser = $parser;
        $this->generatorService = $generatorService;
        $this->parameterParser = $parameterParser;
        $this->stringsHelper = $stringsHelper;
        $this->variableFractionService = $variableFractionService;
        $this->regularExpressions = $regularExpressions;
    }

    /**
     * @param string $expression
     * @param array $replacements
     * @return float
     */
    public function evaluateExpression(string $expression, array $replacements = []): float
    {
        return $this->parser::solve($expression, $replacements);
    }

    /**
     * @param EquationTemplateNP $data
     * @return EquationTemplateNP
     * @throws \App\TeacherModule\Exceptions\NewtonApiException
     * @throws \App\TeacherModule\Exceptions\NewtonApiRequestException
     * @throws \App\TeacherModule\Exceptions\NewtonApiSyntaxException
     * @throws \App\TeacherModule\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
        public function processVariableFractions(EquationTemplateNP $data): EquationTemplateNP
    {
        if (!$fractionsProcessed = $this->variableFractionService->processVariableFractions($data)) {
            return $data;
        }
        $data = $this->variableFractionService->getMultipliedByLCM($fractionsProcessed);
        $data->setStandardized($this->stringsHelper::fillMultipliers($data->getStandardized()));
        return $data;
    }

    /**
     * @param EquationTemplateNP $standardized
     * @param array $parValuesArr
     * @param bool $withLinearCoefficient
     * @return array
     */
    public function extractVariableCoefficients(EquationTemplateNP $standardized, array $parValuesArr, bool $withLinearCoefficient = true): array
    {
        $final = $this->parameterParser->passValues($standardized->getStandardized(), $parValuesArr);

        if ($withLinearCoefficient) {
            $matches = Strings::matchAll($final, '~' . sprintf($this->regularExpressions::RE_VARIABLE_COEFFICIENT, $standardized->getVariable()) . '~');
        } else {
            $matches = Strings::matchAll($final, '~' . sprintf($this->regularExpressions::RE_VARIABLE_COEFFICIENT_NON_LINEAR, $standardized->getVariable()) . '~');
        }

        $res = [];
        foreach ($matches as $match) {
            // Substitute coefficients extreme values
            $match[1] = $this->normalizeCoefficient($match[1]);
            $res[] = $match;
        }

        return $res;
    }

    /**
     * @param string $expression
     * @return string
     */
    public function normalizeCoefficient(string $expression): string
    {
        $expression = Strings::trim($expression);

        if ($expression === '') {
            return '1';
        }

        if ($expression === '+') {
            return '1';
        }

        if ($expression === '-') {
            return -1;
        }

        $expression = $this->stringsHelper::normalizeOperators($expression);

        return $expression;
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
     * @return string
     */
    public function negateOperators(string $expression): string
    {
        $startOp = '';
        if (self::firstOperator($expression) === '+') {
            $startOp = '-';
        }
        $expression = $this->stringsHelper::trimOperators($expression);
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
}
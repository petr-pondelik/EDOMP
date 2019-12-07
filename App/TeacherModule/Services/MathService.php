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
use App\TeacherModule\Model\NonPersistent\Entity\EquationTemplateNP;
use jlawrence\eos\Parser;
use Nette\Utils\Strings;

/**
 * Class MathService
 * @package App\TeacherModule\Services
 */
class MathService
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
    public $variableFractionService;

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
            bdump('WITHOUT VARIABLE FRACTIONS');
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
}
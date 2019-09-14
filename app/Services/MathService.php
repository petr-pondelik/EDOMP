<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 18.8.19
 * Time: 18:57
 */

namespace App\Services;

use App\Helpers\RegularExpressions;
use App\Helpers\StringsHelper;
use App\Model\NonPersistent\Entity\EquationTemplateNP;
use App\Model\NonPersistent\Entity\LinearEquationTemplateNP;
use jlawrence\eos\Parser;
use Nette\Utils\Strings;

/**
 * Class MathService
 * @package App\Services
 */
class MathService
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var GeneratorService
     */
    protected $generatorService;

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
     * @param GeneratorService $generatorService
     * @param StringsHelper $stringsHelper
     * @param VariableFractionService $variableFractionService
     * @param RegularExpressions $regularExpressions
     */
    public function __construct
    (
        Parser $parser, GeneratorService $generatorService, StringsHelper $stringsHelper,
        VariableFractionService $variableFractionService, RegularExpressions $regularExpressions
    )
    {
        $this->parser = $parser;
        $this->generatorService = $generatorService;
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
     * @return LinearEquationTemplateNP
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiSyntaxException
     * @throws \App\Exceptions\NewtonApiUnreachableException
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
        $final = $this->stringsHelper::passValues($standardized->getStandardized(), $parValuesArr);

        if($withLinearCoefficient){
            $matches = Strings::matchAll($final, '~' . sprintf($this->regularExpressions::RE_VARIABLE_COEFFICIENT, $standardized->getVariable()) . '~');
        }
        else{
            $matches = Strings::matchAll($final, '~' . sprintf($this->regularExpressions::RE_VARIABLE_COEFFICIENT_NON_LINEAR, $standardized->getVariable()) . '~');
        }

        $res = [];
        foreach ($matches as $match){
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

        if($expression === ''){
            return '1';
        }

        if($expression === '+'){
            return '1';
        }

        if($expression === '-'){
            return -1;
        }

        $expression = $this->stringsHelper::normalizeOperators($expression);

        return $expression;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.7.19
 * Time: 21:06
 */

namespace App\Plugins;

use App\Exceptions\InvalidParameterException;
use App\Helpers\ConstHelper;
use App\Helpers\LatexHelper;
use App\Helpers\RegularExpressions;
use App\Helpers\StringsHelper;
use App\Services\VariableFractionService;
use App\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\Model\Persistent\Entity\ProblemFinal;
use App\Model\Persistent\Functionality\TemplateJsonDataFunctionality;
use App\Services\ConditionService;
use App\Services\MathService;
use App\Services\NewtonApiClient;
use jlawrence\eos\Parser;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class ProblemPlugin
 * @package App\Plugins
 */
abstract class ProblemPlugin
{
    /**
     * @var NewtonApiClient
     */
    protected $newtonApiClient;

    /**
     * @var MathService
     */
    protected $mathService;

    /**
     * @var ConditionService
     */
    protected $conditionService;

    /**
     * @var TemplateJsonDataFunctionality
     */
    protected $templateJsonDataFunctionality;

    /**
     * @var LatexHelper
     */
    protected $latexHelper;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * @var RegularExpressions
     */
    protected $regularExpressions;

    /**
     * @var VariableFractionService
     */
    protected $variableDividers;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * ProblemPlugin constructor.
     * @param NewtonApiClient $newtonApiClient
     * @param MathService $mathService
     * @param ConditionService $conditionService
     * @param TemplateJsonDataFunctionality $templateJsonDataFunctionality
     * @param LatexHelper $latexHelper
     * @param StringsHelper $stringsHelper
     * @param VariableFractionService $variableDividers
     * @param ConstHelper $constHelper
     * @param RegularExpressions $regularExpressions
     * @param Parser $parser
     */
    public function __construct
    (
        NewtonApiClient $newtonApiClient,
        MathService $mathService,
        ConditionService $conditionService,
        TemplateJsonDataFunctionality $templateJsonDataFunctionality,
        LatexHelper $latexHelper, StringsHelper $stringsHelper, VariableFractionService $variableDividers,
        ConstHelper $constHelper, RegularExpressions $regularExpressions, Parser $parser
    )
    {
        $this->newtonApiClient = $newtonApiClient;
        $this->mathService = $mathService;
        $this->conditionService = $conditionService;
        $this->templateJsonDataFunctionality = $templateJsonDataFunctionality;
        $this->latexHelper = $latexHelper;
        $this->stringsHelper = $stringsHelper;
        $this->variableDividers = $variableDividers;
        $this->constHelper = $constHelper;
        $this->regularExpressions = $regularExpressions;
        $this->parser = $parser;
    }

    /**
     * @param $expression
     * @return float
     */
    public function evaluateExpression($expression): float
    {
        return $this->parser::solve($expression);
    }

    /**
     * @param string $expression
     * @throws InvalidParameterException
     */
    protected function validateParameters(string $expression): void
    {
        bdump('VALIDATE PARAMETERS');

        if(!Strings::match($expression,'~' . $this->regularExpressions::RE_PARAMETER_OPENING . '~')) {
            throw new InvalidParameterException('Zadaná šablona neobsahuje parametr.');
        }

        $split = $this->stringsHelper::findParametersAll($expression);

        bdump($split);

        foreach ($split as $part) {
            if ($part !== '' && Strings::startsWith($part, '<par')) {

                if (!Strings::match($part, '~' . $this->regularExpressions::RE_PARAMETER_VALID . '~')) {
                    throw new InvalidParameterException('Zadaná šablona obsahuje nevalidní parametr.');
                }

                $min = $this->stringsHelper::extractParAttr($part, 'min');
                $max = $this->stringsHelper::extractParAttr($part, 'max');

                if ($min > $max) {
                    throw new InvalidParameterException('Neplatný interval parametru.');
                }
            }
        }

    }

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return int
     */
    abstract public function validateBody(ProblemTemplateNP $problemTemplate): int;

    /**
     * @param ProblemTemplateNP $expression
     * @return ProblemTemplateNP
     */
    abstract public function standardize(ProblemTemplateNP $expression): ProblemTemplateNP;

    /**
     * @param ProblemFinal $problem
     * @return ArrayHash
     */
    abstract public function evaluate(ProblemFinal $problem): ArrayHash;
}
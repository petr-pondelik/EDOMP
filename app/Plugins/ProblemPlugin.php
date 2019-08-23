<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.7.19
 * Time: 21:06
 */

namespace App\Plugins;

use App\Arguments\BodyArgument;
use App\Arguments\ProblemValidateArgument;
use App\Exceptions\InvalidParameterException;
use App\Helpers\ConstHelper;
use App\Helpers\LatexHelper;
use App\Helpers\StringsHelper;
use App\Helpers\VariableDividers;
use App\Model\NonPersistent\ProblemTemplateNP;
use App\Model\Persistent\Entity\ProblemFinal;
use App\Model\Persistent\Functionality\TemplateJsonDataFunctionality;
use App\Services\ConditionService;
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

    /**
     * @var NewtonApiClient
     */
    protected $newtonApiClient;

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
     * @var VariableDividers
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
     * @param ConditionService $conditionService
     * @param TemplateJsonDataFunctionality $templateJsonDataFunctionality
     * @param LatexHelper $latexHelper
     * @param StringsHelper $stringsHelper
     * @param VariableDividers $variableDividers
     * @param ConstHelper $constHelper
     * @param Parser $parser
     */
    public function __construct
    (
        NewtonApiClient $newtonApiClient,
        ConditionService $conditionService,
        TemplateJsonDataFunctionality $templateJsonDataFunctionality,
        LatexHelper $latexHelper, StringsHelper $stringsHelper, VariableDividers $variableDividers,
        ConstHelper $constHelper, Parser $parser
    )
    {
        $this->newtonApiClient = $newtonApiClient;
        $this->conditionService = $conditionService;
        $this->templateJsonDataFunctionality = $templateJsonDataFunctionality;
        $this->latexHelper = $latexHelper;
        $this->stringsHelper = $stringsHelper;
        $this->variableDividers = $variableDividers;
        $this->constHelper = $constHelper;
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
        $split = $this->stringsHelper::splitByParameterBase($expression);

        if(!Strings::match($expression,'~\<par.*\>~')) {
            throw new InvalidParameterException('Zadaná šablona neobsahuje parametr.');
        }

        foreach ($split as $part) {
            if ($part !== '' && Strings::startsWith($part, '<par')) {
                bdump(Strings::match($part, '~<par min=\"\-?[0-9]+\" max=\"\-?[0-9]+\"/>~'));
                if (!Strings::match($part, '~<par min=\"\-?[0-9]+\" max=\"\-?[0-9]+\"/>~')) {
                    throw new InvalidParameterException('Zadaná šablona obsahuje nevalidní parametr.');
                } else {
                    $min = $this->stringsHelper::extractParAttr($part, 'min');
                    $max = $this->stringsHelper::extractParAttr($part, 'max');
                    bdump([$min, $max]);
                    if ($min > $max) {
                        throw new InvalidParameterException('Neplatný interval parametru.');
                    }
                }
            }
        }

    }

    /**
     * @param string $variable
     * @return mixed
     */
    abstract public static function getRegExp(string $variable): string;

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

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return int
     */
    abstract public function validateBody(ProblemTemplateNP $problemTemplate): int;
}
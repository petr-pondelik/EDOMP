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
use App\Model\Entity\ProblemFinal;
use App\Model\Functionality\TemplateJsonDataFunctionality;
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
     * @param ConstHelper $constHelper
     * @param Parser $parser
     */
    public function __construct
    (
        NewtonApiClient $newtonApiClient,
        ConditionService $conditionService,
        TemplateJsonDataFunctionality $templateJsonDataFunctionality,
        LatexHelper $latexHelper, StringsHelper $stringsHelper, ConstHelper $constHelper,
        Parser $parser
    )
    {
        $this->newtonApiClient = $newtonApiClient;
        $this->conditionService = $conditionService;
        $this->templateJsonDataFunctionality = $templateJsonDataFunctionality;
        $this->latexHelper = $latexHelper;
        $this->stringsHelper = $stringsHelper;
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
        $split = $this->stringsHelper::splitByParameters($expression, true);

        if (count($split) <= 1) {
            throw new InvalidParameterException('Zadaná šablona neobsahuje parametr.');
        }

        foreach ($split as $part) {
            if ($part !== '' && Strings::startsWith($part, '<par')) {
                if (!Strings::match($part, '~<par min="[0-9]+" max="[0-9]+"/>~')) {
                    throw new InvalidParameterException('Zadaná šablona obsahuje nevalidní parametr.');
                } else {
                    $min = $this->stringsHelper::extractParAttr($part, 'min');
                    $max = $this->stringsHelper::extractParAttr($part, 'max');
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
     * @param string $expression
     * @return string
     */
    abstract public function standardize(string $expression): string;

    /**
     * @param ProblemFinal $problem
     * @return ArrayHash
     */
    abstract public function evaluate(ProblemFinal $problem): ArrayHash;

    /**
     * @param BodyArgument $argument
     * @return int
     */
    abstract public function validateBody(BodyArgument $argument): int;
}
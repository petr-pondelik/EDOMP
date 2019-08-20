<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 18.8.19
 * Time: 18:57
 */

namespace App\Services;

use App\Helpers\StringsHelper;
use jlawrence\eos\Parser;

/**
 * Class EosParserWrapper
 * @package App\Services
 */
class EosParserWrapper
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * EosParserWrapper constructor.
     * @param Parser $parser
     * @param StringsHelper $stringsHelper
     */
    public function __construct(Parser $parser, StringsHelper $stringsHelper)
    {
        $this->parser = $parser;
        $this->stringsHelper = $stringsHelper;
    }

    /**
     * @param string $expression
     * @param string|null $variable
     * @return float
     */
    public function evaluateExpression(string $expression, string $variable = null): float
    {
        bdump($expression);
//        $expression = $this->stringsHelper::fillMultipliers($expression, $variable);
//        bdump($expression);
        return $this->parser::solve($expression);
    }
}
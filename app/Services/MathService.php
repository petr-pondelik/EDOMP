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
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * MathService constructor.
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
//        $expression = $this->stringsHelper::fillMultipliers($expression, $variable);
        return $this->parser::solve($expression);
    }
}
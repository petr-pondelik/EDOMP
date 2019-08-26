<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.8.19
 * Time: 0:22
 */

namespace App\Helpers;

use App\Model\NonPersistent\Math\GlobalDivider;
use App\Model\NonPersistent\Math\VariableFraction;
use App\Services\NewtonApiClient;
use Nette\Utils\Strings;

/**
 * Class VariableDividers
 * @package App\Helpers
 */
class VariableDividers
{
    /**
     * @const int
     */
    protected const EXPRESSION = 0;

    /**
     * @const int
     */
    protected const OPERATOR = 1;

    /**
     * @const int
     */
    protected const FACTOR = 2;

    /**
     * @const int
     */
    protected const DIVIDER = 3;

    /**
     * @const string
     */
    protected const RE_VARIABLE_STANDARDIZED_DIVIDER = '~' . '(\+|\-|)' . '([px\d\s\^]+)' . '\/\s*' . '(\([px\-\+\s\(\)\d]+\))' . '~';

    /**
     * @var NewtonApiClient
     */
    protected $newtonApiClient;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * @var string
     */
    protected $expression;

    /**
     * @var string
     */
    protected $multiplied;

    /**
     * @var VariableFraction[]
     */
    protected $varFractions;

    /**
     * @var GlobalDivider
     */
    protected $globalDivider;

    /**
     * VariableDividers constructor.
     * @param NewtonApiClient $newtonApiClient
     * @param StringsHelper $stringsHelper
     */
    public function __construct(NewtonApiClient $newtonApiClient, StringsHelper $stringsHelper)
    {
        $this->newtonApiClient = $newtonApiClient;
        $this->stringsHelper = $stringsHelper;
        $this->globalDivider = new GlobalDivider($stringsHelper);
    }

    /**
     * @param string $expression
     * @param string $nonStandardized
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiSyntaxException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setData(string $expression, string $nonStandardized): void
    {
        bdump('VARIABLE DIVIDERS: SET DATA');

        $this->expression = $expression;
        $varFractionsData = Strings::matchAll($expression, self::RE_VARIABLE_STANDARDIZED_DIVIDER);
        bdump($this->stringsHelper::deduplicateBrackets($nonStandardized));
        bdump(Strings::matchAll($nonStandardized,self::RE_VARIABLE_STANDARDIZED_DIVIDER));

        bdump($varFractionsData);
        $this->globalDivider->setOriginalFactors($varFractionsData);

        foreach ($varFractionsData as $key => $varFractionData) {

            $divider = $this->getDivider($varFractionData[self::DIVIDER]);

            $variableFraction = new VariableFraction($varFractionData);
            $variableFraction->setCoefficient($divider['coefficient']);
            $variableFraction->setFactors($divider['factors']);
            $this->varFractions[] = $variableFraction;

            foreach ($divider['factors'] as $factor){
                $this->globalDivider->addDividerFactor($factor);
            }
            $this->globalDivider->raiseDividerCoefficient($divider['coefficient']);
            $this->globalDivider->addFactor($varFractionData[self::FACTOR]);

        }

        bdump($this->varFractions);
    }

    /**
     * @return string
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMultiplied(): string
    {
        bdump('GET MULTIPLIED');

        $this->multiplied = $this->expression;

        bdump($this->varFractions);
        if($this->varFractions && count($this->varFractions)){

            bdump('WITH VAR DIVIDERS');

            $varFractionsExpression = '';

            foreach ($this->varFractions as $key => $varFraction){
                $this->multiplied = $this->stringsHelper::removeSubstring($this->multiplied, $varFraction->getExpression());
                $multipliers = $this->globalDivider->getDividerCoefficient() / $varFraction->getCoefficient();
                foreach ($this->globalDivider->getDividerFactors() as $factor){
                    if(!in_array($factor, $varFraction->getFactors(), true)){
                        $multipliers .= $this->stringsHelper::wrap($factor);
                    }
                }

                $varFractionsExpression .= ' ' . $varFraction->getOperator() . $varFraction->getFactor() . $multipliers;
            }

            $this->multiplied = ($this->stringsHelper::removeWhiteSpaces($this->multiplied) ? $this->globalDivider->getDividerCoefficient() . '*' . $this->globalDivider->getDividerFactorsString() . $this->stringsHelper::wrap($this->multiplied) : '')
                                . '+ ' . $this->stringsHelper::wrap($varFractionsExpression);

            $this->multiplied = $this->newtonApiClient->simplify($this->multiplied);

        }
        else{
            bdump('WITHOUT VAR DIVIDERS');
        }

        return $this->multiplied;
    }

    /**
     * @param string $divider
     * @return array
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiSyntaxException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getDivider(string $divider): array
    {
        bdump('GET DIVIDER');
        $res = [];

        $factored = $this->newtonApiClient->factor($divider);

        $coefficient = Strings::trim(Strings::before($factored, '('));
        $coefficient = $coefficient !== '' ? $coefficient : '1';

        $withoutCoefficient = Strings::after($factored, '(');
        $withoutCoefficient = $withoutCoefficient ?: $factored;

        $res['coefficient'] = $coefficient;
        $dividers = explode(') (', $withoutCoefficient);

        foreach ($dividers as $item){
            $res['factors'][] = $this->stringsHelper::trim($item);
        }

        return $res;
    }

    /**
     * @return GlobalDivider
     */
    public function getGlobalDivider(): GlobalDivider
    {
        return $this->globalDivider;
    }
}
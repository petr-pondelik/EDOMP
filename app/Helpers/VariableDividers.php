<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.8.19
 * Time: 0:22
 */

namespace App\Helpers;

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
     * @var array
     */
    protected $varFractions = [];

    /**
     * @var array
     */
    protected $allVarDividers = [];

    /**
     * VariableDividers constructor.
     * @param NewtonApiClient $newtonApiClient
     * @param StringsHelper $stringsHelper
     */
    public function __construct(NewtonApiClient $newtonApiClient, StringsHelper $stringsHelper)
    {
        $this->newtonApiClient = $newtonApiClient;
        $this->stringsHelper = $stringsHelper;
        $this->allVarDividers['coefficient'] = 1;
    }

    /**
     * @param string $expression
     * @param array $varFractions
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiSyntaxException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setData(string $expression, array $varFractions): void
    {
        bdump('VARIABLE DIVIDERS: SET DATA');

        $this->expression = $expression;
        $this->varFractions = $varFractions;

        foreach ($varFractions as $key => $varFraction){
            $divider = $this->getDivider($varFraction[self::DIVIDER]);
            $this->varFractions[$key][self::DIVIDER] = $divider;

            foreach ($divider['factors'] as $factor){
                $factor = $this->stringsHelper::trim($factor);
                $this->addDivider($factor);
            }

            $this->raiseCoefficient($divider['coefficient']);
        }
    }

    /**
     * @param bool $toString
     * @return array|string
     */
    public function getAllVarDividers(bool $toString = false)
    {
        if($toString){
            $res = '';
            foreach ($this->allVarDividers['factors'] as $divider){
                $res .= $this->stringsHelper::wrap($divider);
            }
            return $res;
        }
        return $this->allVarDividers;
    }

    /**
     * @return string
     */
    public function getMultiplied(): string
    {
        bdump('GET MULTIPLIED');

        $this->multiplied = $this->expression;
        $varFractionsExpression = '';

        foreach ($this->varFractions as $key => $varFraction){
            $this->multiplied = $this->stringsHelper::removeSubstring($this->multiplied, $varFraction[self::EXPRESSION]);
            $partialDivider = $varFraction[self::DIVIDER];
//            $multipliers = $partialDivider['coefficient'] === '1' ? $this->allVarDividers['coefficient'] : $partialDivider['coefficient'];
            $multipliers = (int) $this->allVarDividers['coefficient'] / (int) $partialDivider['coefficient'];
            foreach ($this->allVarDividers['factors'] as $divider){
                if(!in_array($divider, $partialDivider['factors'], true)){
                    $multipliers .= $this->stringsHelper::wrap($divider);
                }
            }

            $varFraction[0] = $varFraction[1] . $varFraction[2] . $multipliers;
            $varFractionsExpression .= ' ' . $varFraction[0];
        }

        $this->multiplied = ($this->stringsHelper::removeWhiteSpaces($this->multiplied) ? $this->allVarDividers['coefficient'] . '*' . $this->getAllVarDividers(true) . $this->stringsHelper::wrap($this->multiplied) : '')
                            . '+ ' . $this->stringsHelper::wrap($varFractionsExpression);

        return $this->multiplied;
    }

    /**
     * @param string $divider
     */
    protected function addDivider(string $divider): void
    {
        if(!isset($this->varDividers[$divider])){
            $this->allVarDividers['factors'][$divider] = $divider;
        }
    }

    /**
     * @param int $coefficient
     */
    protected function raiseCoefficient(int $coefficient): void
    {
        $this->allVarDividers['coefficient'] *= $coefficient;
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
}
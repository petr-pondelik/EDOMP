<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.8.19
 * Time: 0:22
 */

namespace App\Services;

use App\Helpers\StringsHelper;
use App\Model\NonPersistent\Entity\EquationTemplateNP;
use App\Model\NonPersistent\Math\GlobalDivider;
use App\Model\NonPersistent\Math\LocalDivider;
use App\Model\NonPersistent\Math\VariableFraction;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class VariableFractionService
 * @package App\Services
 */
class VariableFractionService
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
    protected const RE_VARIABLE_STANDARDIZED_DIVIDER = '~' . '((\+|\-|)[px\d\s\^]+)' . '\/\s*' . '(\([px\-\+\s\(\)\d]+\))' . '~';

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
     * @var VariableFraction[]
     */
    protected $varFractionsParDivider;

    /**
     * @var GlobalDivider
     */
    protected $globalDivider;

    /**
     * VariableFractionService constructor.
     * @param NewtonApiClient $newtonApiClient
     * @param StringsHelper $stringsHelper
     */
    public function __construct(NewtonApiClient $newtonApiClient, StringsHelper $stringsHelper)
    {
        $this->newtonApiClient = $newtonApiClient;
        $this->stringsHelper = $stringsHelper;
        $this->globalDivider = new GlobalDivider();
        $this->varFractions = [];
        $this->varFractionsParDivider = [];
    }

    /**
     * @param EquationTemplateNP $data
     * @return GlobalDivider|null
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiSyntaxException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getVariableFractionData(EquationTemplateNP $data): ?EquationTemplateNP
    {
        bdump('VARIABLE DIVIDERS: SET DATA');

        $this->expression = $data->getStandardized();
        $varFractionsData = Strings::matchAll($this->expression, self::RE_VARIABLE_STANDARDIZED_DIVIDER);

        if(!$varFractionsData){
            return null;
        }

        $globalDivider = new GlobalDivider();
        $varFractions = [];
        $varFractionsParametrized = [];
        $groupedVarFractions = $this->groupVariableFractions($varFractionsData);
        bdump($groupedVarFractions);

        foreach ($groupedVarFractions as $key => $variableFraction){

            $localDivider = $this->getLocalDivider($variableFraction->getDivider());

            $variableFraction->setCoefficient($localDivider->getCoefficient());
            $variableFraction->setFactors($localDivider->getFactors());
            $variableFraction->setDivider($localDivider->getFactored());
            $variableFraction->setParametrized($variableFraction->hasParameters());

            $varFractions[] = $variableFraction;
            if($variableFraction->isParametrized()){
                $varFractionsParametrized[] = $variableFraction;
            }

            foreach ($localDivider->getFactors() as $factor){
                $globalDivider->addLCMFactor($this->stringsHelper::trim($factor));
            }

//            if(!$globalDivider->hasFactoredDivider($localDivider->getFactored())){
            $globalDivider->raiseLCMCoefficient($localDivider->getCoefficient());
//            }

//            $globalDivider->addFactoredDivider($localDivider->getFactored());
//            $globalDivider->addNumerator($this->stringsHelper::trim($variableFraction->getNumerator()));

        }

        $globalDivider->setLcmExpression($globalDivider->getLCMString());

        $data->setGlobalDivider($globalDivider);
        $data->setVarFractions($varFractions);
        $data->setVarFractionsParametrized($varFractionsParametrized);
        $data->setFractionsToCheckCnt($data->calculateFractionsToCheckCnt());

        return $data;

//        bdump($varFractionsData);
//        if(!$varFractionsData){
//            return false;
//        }
//
//        $groupedVarFractions = $this->groupVarFractions($varFractionsData);
//
//        foreach ($groupedVarFractions as $key => $fraction) {
//
//            $divider = $this->getDivider($fraction[self::DIVIDER]);
//            bdump('DIVIDER');
//            bdump($divider);
//
//            $variableFraction = new VariableFraction($fraction);
//            $variableFraction->setCoefficient($divider['coefficient']);
//            $variableFraction->setFactors($divider['factors']);
//            $variableFraction->setFactoredDivider($divider['factored']);
//            $variableFraction->setDividerParametrized( Strings::match($divider['factored'], '~p\d+~') ? true : false );
//
//            $this->varFractions[] = $variableFraction;
//            if($variableFraction->isDividerParametrized()){
//                $this->varFractionsParDivider[] = $variableFraction;
//            }
//
//            foreach ($divider['factors'] as $factor){
//                $this->globalDivider->addLCMFactor($this->stringsHelper::trim($factor));
//            }
//
//            if(!$this->globalDivider->hasFactoredDivider($divider['factored'])){
//                $this->globalDivider->raiseLCMCoefficient($divider['coefficient']);
//            }
//
//            $this->globalDivider->addFactoredDivider($divider['factored']);
//            $this->globalDivider->addNumerator($this->stringsHelper::trim($fraction[self::FACTOR]));
//
//        }
//
//        bdump('VAR FRACTIONS PAR DIVIDER');
//        bdump($this->varFractionsParDivider);
//
//        return true;
    }

    /**
     * @param EquationTemplateNP $data
     * @return EquationTemplateNP
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMultipliedByLCM(EquationTemplateNP $data): EquationTemplateNP
    {
        bdump('GET MULTIPLIED');

        bdump($data->getGlobalDivider());

        if($data->getVarFractions()){

            bdump('HAS VAR FRACTIONS');
            bdump($data->getVarFractions());

            $variableFractionsExpression = '';
            $multiplied = $data->getStandardized();

            foreach ($data->getVarFractions() as $key => $varFraction){
                $multiplied = $this->stringsHelper::removeSubstring($data->getStandardized(), $varFraction->getExpression());
                $multiplier = ' ' . ($data->getGlobalDivider()->getLCMCoefficient() / $varFraction->getCoefficient()) . ' ';

                foreach ($data->getGlobalDivider()->getLCMFactors() as $LCMFactor){
                    bdump($LCMFactor);
                    bdump($varFraction->getFactors());
                    if(!in_array($LCMFactor, $varFraction->getFactors(), true)){
                        $multiplier .= $this->stringsHelper::wrap($LCMFactor);
                    }
                }

                $variableFractionsExpression .= ($key ? ' +' : ' ') . $multiplier . ' ' . $varFraction->getNumerator();
            }

            bdump($variableFractionsExpression);
            bdump('MULTIPLIED');
            bdump($multiplied);

            $multiplied = ($this->stringsHelper::removeWhiteSpaces($multiplied) ? $this->globalDivider->getLCMCoefficient() . '*' . $this->globalDivider->getLCMExpression() . $this->stringsHelper::wrap($multiplied) : '')
                            . '+ ' . $this->stringsHelper::wrap($variableFractionsExpression);
            bdump($multiplied);
            $multiplied = $this->newtonApiClient->simplify($multiplied);

            $data->setStandardized($multiplied);

        }
        else {
            bdump('HAS NO VAR FRACTIONS');
        }

        return $data;

//        bdump($this->getGlobalDivider());
//
//        $this->multiplied = $this->expression;
//        bdump($this->multiplied);
//
//        if($this->varFractions && count($this->varFractions)){
//
//            bdump('WITH VAR DIVIDERS');
//            bdump($this->varFractions);
//
//            $varFractionsExpression = '';
//
//            foreach ($this->varFractions as $key => $varFraction){
//                $this->multiplied = $this->stringsHelper::removeSubstring($this->multiplied, $varFraction->getExpression());
//                $multipliers = $this->globalDivider->getLCMCoefficient() / $varFraction->getCoefficient();
//                foreach ($this->globalDivider->getLCMFactors() as $factor){
//                    if(!in_array($factor, $varFraction->getFactors(), true)){
//                        $multipliers .= $this->stringsHelper::wrap($factor);
//                    }
//                }
//
//                $varFractionsExpression .= ' ' . $varFraction->getNumerator() . $multipliers;
//            }
//
//            $this->multiplied = ($this->stringsHelper::removeWhiteSpaces($this->multiplied) ? $this->globalDivider->getLCMCoefficient() . '*' . $this->globalDivider->getLCMFactorsString() . $this->stringsHelper::wrap($this->multiplied) : '')
//                                . '+ ' . $this->stringsHelper::wrap($varFractionsExpression);
//
//            $this->multiplied = $this->newtonApiClient->simplify($this->multiplied);
//
//        }
//        else{
//            bdump('WITHOUT VAR DIVIDERS');
//        }
//
//        return $this->multiplied;
    }

    /**
     * @param string $divider
     * @return LocalDivider
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiSyntaxException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getLocalDivider(string $divider): LocalDivider
    {
        bdump('GET DIVIDER');

        $res = new LocalDivider();

        $factored = $this->newtonApiClient->factor($divider);
        $res->setFactored($factored);

        $coefficient = Strings::trim(Strings::before($factored, '('));
        $res->setCoefficient((int) $coefficient);

        $withoutCoefficient = Strings::after($factored, '(');
        $withoutCoefficient = $withoutCoefficient ?: $factored;

        $factors = explode(') (', $withoutCoefficient);

        foreach ($factors as $factor){
            $res->addFactor($this->stringsHelper::trim($factor));
        }

        return $res;

//        $res = [];
//
//        $factored = $this->newtonApiClient->factor($divider);
//
//        $coefficient = Strings::trim(Strings::before($factored, '('));
//        $coefficient = $coefficient !== '' ? $coefficient : '1';
//
//        $withoutCoefficient = Strings::after($factored, '(');
//        $withoutCoefficient = $withoutCoefficient ?: $factored;
//
//        $res['coefficient'] = $coefficient;
//        $dividers = explode(') (', $withoutCoefficient);
//
//        foreach ($dividers as $item){
//            $res['factors'][] = $this->stringsHelper::trim($item);
//        }
//        $res['factored'] = $factored;
//
//        return $res;
    }

    /**
     * @param array $variableFractionsData
     * @return array
     */
    public function groupVariableFractions(array $variableFractionsData): array
    {
        bdump('GROUP VARIABLE FRACTIONS');

        $usedDividers = [];
        $res = [];
        $cnt = count($variableFractionsData);

        foreach ($variableFractionsData as $key => $data){
            if(!in_array($data[3], $usedDividers, true)){
                $divider = $data[3];
                $numerator = '';

                for($i = 0; $i < $cnt; $i++){
                    if($variableFractionsData[$i][3] === $divider){
                        $numerator .= $variableFractionsData[$i][1];
                    }
                }

                $tempData['numerator'] = $this->stringsHelper::wrap(Strings::trim($numerator));
                $tempData['expression'] = $this->stringsHelper::wrap($this->stringsHelper::trim($numerator)) . ' / ' . $data[3];
                $tempData['divider'] = $data[3];

                $usedDividers[] = $data[3];

                $res[] = new VariableFraction(ArrayHash::from($tempData));
            }
        }

        bdump('GROUP VARIABLE FRACTIONS RES');
        bdump($res);
        return $res;
    }

    /**
     * @return GlobalDivider
     */
    public function getGlobalDivider(): GlobalDivider
    {
        return $this->globalDivider;
    }

    /**
     * @return VariableFraction[]
     */
    public function getVarFractions(): array
    {
        return $this->varFractions;
    }

    /**
     * @param VariableFraction[] $varFractions
     */
    public function setVarFractions(array $varFractions): void
    {
        $this->varFractions = $varFractions;
    }

    /**
     * @return VariableFraction[]
     */
    public function getVarFractionsParDivider(): array
    {
        return $this->varFractionsParDivider;
    }

    /**
     * @param VariableFraction[] $varFractionsParDivider
     */
    public function setVarFractionsParDivider(array $varFractionsParDivider): void
    {
        $this->varFractionsParDivider = $varFractionsParDivider;
    }
}
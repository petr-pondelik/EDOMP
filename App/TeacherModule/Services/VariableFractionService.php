<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.8.19
 * Time: 0:22
 */

namespace App\TeacherModule\Services;

use App\CoreModule\Helpers\RegularExpressions;
use App\CoreModule\Helpers\StringsHelper;
use App\TeacherModule\Model\NonPersistent\Entity\EquationTemplateNP;
use App\TeacherModule\Model\NonPersistent\Math\GlobalDivider;
use App\TeacherModule\Model\NonPersistent\Math\LocalDivider;
use App\TeacherModule\Model\NonPersistent\Math\NonDegradeCondition;
use App\TeacherModule\Model\NonPersistent\Math\Numerator;
use App\TeacherModule\Model\NonPersistent\Math\VariableFraction;
use Nette\Utils\Strings;

/**
 * Class VariableFractionService
 * @package App\TeacherModule\Services
 */
final class VariableFractionService
{
    /**
     * @const int
     */
    protected const EXPRESSION = 0;

    /**
     * @const int
     */
    protected const NUMERATOR = 1;

    /**
     * @const int
     */
    protected const DIVIDER = 2;

    /**
     * @var NewtonApiClient
     */
    protected $newtonApiClient;

    /**
     * @var ParameterParser
     */
    protected $parameterParser;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * @var RegularExpressions
     */
    protected $regularExpressions;

    /**
     * VariableFractionService constructor.
     * @param NewtonApiClient $newtonApiClient
     * @param ParameterParser $parameterParser
     * @param StringsHelper $stringsHelper
     * @param RegularExpressions $regularExpressions
     */
    public function __construct
    (
        NewtonApiClient $newtonApiClient,
        ParameterParser $parameterParser,
        StringsHelper $stringsHelper,
        RegularExpressions $regularExpressions
    )
    {
        $this->newtonApiClient = $newtonApiClient;
        $this->parameterParser = $parameterParser;
        $this->stringsHelper = $stringsHelper;
        $this->regularExpressions = $regularExpressions;
    }

    /**
     * @param EquationTemplateNP $data
     * @return GlobalDivider|null
     * @throws \App\TeacherModule\Exceptions\NewtonApiException
     * @throws \App\TeacherModule\Exceptions\NewtonApiRequestException
     * @throws \App\TeacherModule\Exceptions\NewtonApiSyntaxException
     * @throws \App\TeacherModule\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processVariableFractions(EquationTemplateNP $data): ?EquationTemplateNP
    {
        bdump('PROCESS VARIABLE FRACTIONS');
        $varFractionsData = Strings::matchAll($data->getStandardized(), sprintf($this->regularExpressions::RE_VARIABLE_STANDARDIZED_FRACTION, $data->getVariable(), $data->getVariable(), $data->getVariable()));
        $varFractionsDataRes = [];

        foreach ($varFractionsData as $key => $item) {
            if (Strings::contains($item[self::EXPRESSION], $data->getVariable())) {
                $varFractionsDataRes[] = $item;
            }
        }

        if (!$varFractionsDataRes) {
            return null;
        }

        $data->setVariableFractionsData($varFractionsDataRes);
        $data = $this->processVariableFractionsFirstRound($data, $varFractionsData);
        $data = $this->processVariableFractionsSecondRound($data);
        $data = $this->varFracNonDegradeConditions($data);
        return $data;
    }

    /**
     * @param string $expression
     * @param array $fractions
     * @return string
     */
    public function removeFractionsFromExpression(string $expression, array $fractions): string
    {
        foreach ($fractions as $fraction) {
            $expression = $this->stringsHelper::removeSubstring($expression, $fraction[0]);
        }
        return $expression;
    }

    /**
     * @param EquationTemplateNP $data
     * @return EquationTemplateNP
     * @throws \App\TeacherModule\Exceptions\NewtonApiException
     * @throws \App\TeacherModule\Exceptions\NewtonApiRequestException
     * @throws \App\TeacherModule\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMultipliedByLCM(EquationTemplateNP $data): EquationTemplateNP
    {
        bdump('GET MULTIPLIED BY LCM');
        $data->setStandardized(Strings::trim($this->removeFractionsFromExpression($data->getStandardized(), $data->getVariableFractionsData())));

        if ($data->getVarFractions()) {

            bdump('HAS VAR FRACTIONS');

            $variableFractionsExpression = '';
            $multiplied = $data->getStandardized();

            foreach ($data->getVarFractions() as $key => $varFraction) {
                $multiplier = ' ' . '(' . ($data->getGlobalDivider()->getLCMCoefficient() / $varFraction->getDivider()->getCoefficient()) . ')' . ' ';

                foreach ($data->getGlobalDivider()->getLCMFactors() as $LCMFactor) {
                    if (!in_array($LCMFactor, $varFraction->getDivider()->getFactors(), true)) {
                        $multiplier .= $this->stringsHelper::wrap($LCMFactor);
                    }
                }

                $variableFractionsExpression .= ($key ? ' +' : ' ') . $multiplier . ' ' . $this->stringsHelper::wrap($varFraction->getNumerator()->getExpression());
            }

            $multiplied = ($this->stringsHelper::removeWhiteSpaces($multiplied) ? $data->getGlobalDivider()->getLCMCoefficient() . ' * ' . $data->getGlobalDivider()->getLCMExpression() . ' ' . $this->stringsHelper::wrap($multiplied) : '')
                . ' + ' . $this->stringsHelper::wrap($variableFractionsExpression);
            $multiplied = $this->newtonApiClient->simplify($multiplied);

            $data->setStandardized($multiplied);

        } else {
            bdump('HAS NO VAR FRACTIONS');
        }

        return $data;
    }

    /**
     * @param LocalDivider $localDivider
     * @return LocalDivider
     * @throws \App\TeacherModule\Exceptions\NewtonApiException
     * @throws \App\TeacherModule\Exceptions\NewtonApiRequestException
     * @throws \App\TeacherModule\Exceptions\NewtonApiSyntaxException
     * @throws \App\TeacherModule\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function processLocalDivider(LocalDivider $localDivider): LocalDivider
    {
        bdump('PROCESS LOCAL DIVIDER');

        $factored = $this->newtonApiClient->factor($localDivider->getExpression());
        $localDivider->setFactored($factored);

        $matchArr = Strings::match($factored, '~^([\s\d\-\+]*)(.*)~');
        [$coefficient, $withoutCoefficient] = [$matchArr[1] === '-' ? '-1' : $matchArr[1], $matchArr[2]];

        $withoutCoefficient = $withoutCoefficient ?: $factored;

        $localDivider->setCoefficient((int)$coefficient);

        $withoutCoefficient = $this->stringsHelper::trim($withoutCoefficient);
        $factors = explode('(', $withoutCoefficient);

        foreach ($factors as $factor) {
            $localDivider->addFactor($this->stringsHelper::trim($factor));
        }

        return $localDivider;
    }

    /**
     * @param EquationTemplateNP $data
     * @param array $variableFractionsData
     * @return EquationTemplateNP
     * @throws \App\TeacherModule\Exceptions\NewtonApiException
     * @throws \App\TeacherModule\Exceptions\NewtonApiRequestException
     * @throws \App\TeacherModule\Exceptions\NewtonApiSyntaxException
     * @throws \App\TeacherModule\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processVariableFractionsFirstRound(EquationTemplateNP $data, array $variableFractionsData): EquationTemplateNP
    {
        bdump('GROUP VARIABLE FRACTIONS DATA');

        $usedDividers = [];
        $cnt = count($variableFractionsData);

        $varFractionsStatic = [];
        $varFractionsParametrized = [];
        $globalDivider = new GlobalDivider();

        foreach ($variableFractionsData as $key => $fractionData) {

            if (!in_array($fractionData[self::DIVIDER], $usedDividers, true)) {

                $divider = $fractionData[self::DIVIDER];
                $numerator = '';

                for ($i = 0; $i < $cnt; $i++) {
                    if ($variableFractionsData[$i][self::DIVIDER] === $divider) {
                        $numerator .= $variableFractionsData[$i][self::NUMERATOR];
                    }
                }

                $usedDividers[] = $fractionData[self::DIVIDER];

                $expression = $this->stringsHelper::wrap($this->stringsHelper::trim($numerator)) . ' / ' . $fractionData[self::DIVIDER];
                $numerator = new Numerator($this->stringsHelper::wrap(Strings::trim($numerator)));
                $localDivider = new LocalDivider($fractionData[self::DIVIDER]);

                $variableFraction = new VariableFraction($expression, $localDivider, $numerator);

                $localDivider = $this->processLocalDivider($localDivider);
                $variableFraction->setDivider($localDivider);
                $variableFraction->setParametrized($variableFraction->hasParameters());

                if ($variableFraction->isParametrized()) {
                    $varFractionsParametrized[] = $variableFraction;
                } else {
                    $varFractionsStatic[] = $variableFraction;
                }

                foreach ($localDivider->getFactors() as $factor) {
                    $globalDivider->addLCMFactor($this->stringsHelper::trim($factor));
                }

                $globalDivider->raiseLCMCoefficient(abs($localDivider->getCoefficient()));
            }

            $globalDivider->setLcmExpression($globalDivider->getLCMString());
            $data->setGlobalDivider($globalDivider);
            $data->setVarFractionsStatic($varFractionsStatic);
            $data->setVarFractionsParametrized($varFractionsParametrized);
        }

        return $data;
    }

    /**
     * @param EquationTemplateNP $data
     * @return EquationTemplateNP
     */
    public function processVariableFractionsSecondRound(EquationTemplateNP $data): EquationTemplateNP
    {
        bdump('PROCESS VARIABLE FRACTIONS SECOND ROUND');

        $varFractionsStaticRes = [];
        $varFractionsParametrizedRes = [];
        $toRemove = [];

        $varFractions = $data->getVarFractions();
        $varFractionsTemp = $varFractions;

        foreach ($varFractions as $fractionKey => $fraction){
            unset($varFractionsTemp[$fractionKey]);
            foreach ($varFractionsTemp as $tempKey => $temp){
                if($fractionKey !== $tempKey && $fraction->getDivider()->getFactors() === $temp->getDivider()->getFactors()){
                    $fraction = $fraction->addFraction($temp);
                    $firstCoefficient = $fraction->getDivider()->getCoefficient();
                    $secondCoefficient = $temp->getDivider()->getCoefficient();
                    $reduceLCMBy = $firstCoefficient >= $secondCoefficient ? $secondCoefficient : $firstCoefficient;
                    $data->getGlobalDivider()->reduceLCMCoefficient($reduceLCMBy);
                    $toRemove[] = $tempKey;
                }
            }
        }

        foreach ($varFractions as $key => $fraction){
            if(!in_array($key, $toRemove, true)){
                $fraction->isParametrized() ? ($varFractionsParametrizedRes[] = $fraction) : ($varFractionsStaticRes[] = $fraction);
            }
        }

        $data->setVarFractionsParametrized($varFractionsParametrizedRes);
        $data->setVarFractionsStatic($varFractionsStaticRes);

        return $data;
    }

        /**
     * @param EquationTemplateNP $data
     * @return EquationTemplateNP
     */
    public function varFracNonDegradeConditions(EquationTemplateNP $data): EquationTemplateNP
    {
        bdump('VAR FRAC NON DEGRADE CONDITIONS');

        if (!$parametrizedFractions = $data->getVarFractionsParametrized()) {
            return $data;
        }

        foreach ($parametrizedFractions as $parametrizedFraction) {
            $parametrizedFraction = $this->varFracNonDegradeCond($parametrizedFraction, $data->getVariable());
        }

        $data->setVarFractionsParametrized($parametrizedFractions);
        $data = $this->globalNonDegradeConditions($data);
        return $data;
    }

    /**
     * @param VariableFraction $varFraction
     * @param string $variable
     * @return VariableFraction
     */
    public function varFracNonDegradeCond(VariableFraction $varFraction, string $variable): VariableFraction
    {
        bdump('VAR FRAC NON DEGRADE COND');
        $conditions = [];

        if($varFraction->getNumerator()->isParametrized()){
            $numerator = $this->stringsHelper::normalizeOperators($varFraction->getNumerator()->getExpression());
            $conditions[] = new NonDegradeCondition($numerator, $variable);
        }

        if($varFraction->getDivider()->isParametrized()){
            $divider = $this->stringsHelper::normalizeOperators($varFraction->getDivider()->getExpression());
            $conditions[] = new NonDegradeCondition($divider, $variable);
        }

        $conditions[] = new NonDegradeCondition($varFraction->getDivider()->getExpression() . ' - ' . $this->stringsHelper::wrap($varFraction->getNumerator()->getExpression()), $variable);

        foreach ($conditions as $condition) {
            $expression = $this->stringsHelper::fillMultipliers($this->parameterParser->passValues($condition->getExpression(), [ $variable => 'e' ]));
            $expression = $this->stringsHelper::normalizeOperators($expression);
            $condition->setExpression($expression);
        }

        $varFraction->setNonDegradeConditions($conditions);
        return $varFraction;
    }

    /**
     * @param EquationTemplateNP $data
     * @return EquationTemplateNP
     */
    public function globalNonDegradeConditions(EquationTemplateNP $data): EquationTemplateNP
    {
        bdump('GLOBAL NON DEGRADE CONDITIONS');
        $parametrizedFractions = $data->getVarFractionsParametrized();
        $parametrizedFractionsCnt = count($parametrizedFractions);

        $conditions = [];
        for($i = 1; $i < $parametrizedFractionsCnt; $i++){
            if($parametrizedFractions[$i-1]->getDivider()->isParametrized() || $parametrizedFractions[$i]->getDivider()->isParametrized()){
                $firstWithoutCoeff = $parametrizedFractions[$i-1]->getDivider()->getFactoredWithoutCoefficient();
                $secondWithoutCoeff = $parametrizedFractions[$i]->getDivider()->getFactoredWithoutCoefficient();
                $expression = $this->parameterParser->passValues(sprintf('%s - (%s)', $firstWithoutCoeff, $secondWithoutCoeff), [ $data->getVariable() => 'e' ]);
                $expression = $this->stringsHelper::normalizeOperators($expression);
                $conditions[] = new NonDegradeCondition($expression, $data->getVariable());
            }
        }

        $data->setNonDegradeConditions($conditions);
        return $data;
    }
}
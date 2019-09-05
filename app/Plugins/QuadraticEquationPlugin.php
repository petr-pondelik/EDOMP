<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.7.19
 * Time: 22:21
 */

namespace App\Plugins;

use App\Exceptions\ProblemTemplateException;
use App\Model\NonPersistent\Entity\QuadraticEquationTemplateNP;
use App\Model\Persistent\Entity\ProblemFinal;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;
use Nette\Utils\Strings;

/**
 * Class QuadraticEquationPlugin
 * @package App\Plugins
 */
class QuadraticEquationPlugin extends EquationPlugin
{
    /**
     * @param string $expression
     * @param string $variable
     * @return false|string
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDiscriminantA(string $expression, string $variable)
    {
        bdump('GET DISCRIMINANT A');
        bdump($expression);

        $aExp = Strings::match($expression, '~' . sprintf($this->regularExpressions::RE_DISCRIMINANT_A_COEFFICIENT, $variable) . '~');
        bdump($aExp);

        $prefix = Strings::trim($aExp[1]);
        $postfix = Strings::trim($aExp[2]);

        if($prefix === '' || $prefix === '+'){
            $prefix = '1';
        }

        if($prefix === '-'){
            $prefix = '-1';
        }

        if($postfix !== ''){
            $res = '(' . $postfix . ')^(-1) ' . '(' . $prefix . ')';
        }
        else{
            $res = $prefix;
        }

        bdump($res);

        if($res !== '1' && $res !== '-1'){
            $res = $this->newtonApiClient->simplify($res);
        }

        bdump('GET DISCRIMINANT A RES');
        bdump($res);
        return $this->stringsHelper::wrap($res);
    }

    /**
     * @param string $standardized
     * @param string $variable
     * @return false|string
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDiscriminantB(string $standardized, string $variable)
    {
        bdump('GET DISCRIMINANT B');
        bdump($standardized);

        $bExp = Strings::after($standardized, $variable . '^2');
        $bExp = Strings::replace($bExp, '~' . $this->regularExpressions::RE_FIRST_OPERATOR_SPLIT . '~', '$2$3');
        bdump($bExp);

        $matchArr = Strings::match($bExp, '~' . sprintf($this->regularExpressions::RE_DISCRIMINANT_B_COEFFICIENT, $variable) . '~');
        bdump($matchArr);

        $prefix = Strings::trim($matchArr[1]);
        $postfix = Strings::trim($matchArr[2]);

        if($prefix === '+' || $prefix === ''){
            $prefix = '1';
        }

        if($prefix === '-'){
            $prefix = '-1';
        }

        if($postfix !== ''){
            $res = '(' . $postfix . ')^(-1) ' . '(' . $prefix . ')';
        }
        else{
            $res = $prefix;
        }

        if($res !== '1' && $res !== '-1'){
            $res = $this->newtonApiClient->simplify($res);
        }

        bdump('GET DISCRIMINANT B RES');
        bdump($res);

        return $this->stringsHelper::wrap($res);
    }

    /**
     * @param string $expression
     * @param string $variable
     * @return false|string
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDiscriminantC(string $expression, string $variable)
    {
        bdump('GET DISCRIMINANT C');
        bdump($expression);

        $cExp = Strings::after($expression, ' ' . $variable . ' ');
        $matchArr = Strings::match($cExp, '~' . $this->regularExpressions::RE_DISCRIMINANT_C_COEFFICIENT . '~');
        bdump($matchArr);

        $res = $matchArr[2];

        if(!$res){
            $res = Strings::after($expression, $variable . '^2');
            $matchArr = Strings::match($res, '~' . $this->regularExpressions::RE_DISCRIMINANT_C_COEFFICIENT . '~');
            $res = Strings::trim($matchArr[2]);
            if($res === '' || Strings::contains($res, $variable)){
                bdump('GET DISCRIMINANT C RES');
                bdump('0');
                return '0';
            }
        }

        $res = $this->newtonApiClient->simplify($res);

        bdump('GET DISCRIMINANT C RES');
        bdump($res);
        return $this->stringsHelper::wrap($res);
    }

    /**
     * @param string $standardized
     * @param string $variable
     * @return string
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDiscriminantExpression(string $standardized, string $variable): string
    {
        return $this->stringsHelper::fillMultipliers($this->getDiscriminantB($standardized, $variable) . '^2' . ' - 4 * ' . $this->getDiscriminantA($standardized, $variable) . ' * ' . $this->getDiscriminantC($standardized, $variable));
    }

    /**
     * @param QuadraticEquationTemplateNP $data
     * @return bool
     * @throws ProblemTemplateException
     * @throws \App\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     */
    public function validateType(QuadraticEquationTemplateNP $data): bool
    {
        bdump('VALIDATE QUADRATIC EQUATION');

        // Remove all the spaces
        $standardized = $this->stringsHelper::removeWhiteSpaces($data->getStandardized());
        bdump($standardized);

        // Match string against the quadratic expression regexp
        $matches = Strings::match($standardized, '~' . $this->regularExpressions::getQuadraticEquationRE($data->getVariable()) . '~');

        bdump($matches);

        // Check if the whole expression was matched
        if($matches[0] !== $standardized){
            return false;
        }

        bdump('VARIABLE COEFFICIENTS');
        $variableCoefficients = Strings::matchAll($standardized, '~' . sprintf($this->regularExpressions::RE_VARIABLE_COEFFICIENT, $data->getVariable()) . '~');
        bdump($variableCoefficients);

        foreach ($variableCoefficients as $variableCoefficient){
            if($variableCoefficient[2] > 2){
                if($variableCoefficient[1] === '' || Strings::match($variableCoefficient[1], '~' . $this->regularExpressions::RE_NUM_FRAC . '~')){
                    bdump('FALSE');
                    return false;
                }
            }
        }

        try{
            $matches = $this->conditionService->findConditionsMatches([
                $this->constHelper::QUADRATIC_EQUATION_VALIDATION => [
                    $this->constHelper::IS_QUADRATIC_EQUATION => [
                        'data' => $data
                    ]
                ]
            ]);
        } catch (\Exception $e){
            bdump($e);
            throw new ProblemTemplateException('Zadán chybný formát šablony.');
        }

        bdump($matches);

        if(!$matches){
            // TODO: Handle when there are no parameters matches in validation !!!!
            return false;
        }

        //bdump($matches);

        $matchesJson = Json::encode($matches);
        $this->templateJsonDataFunctionality->create(ArrayHash::from([
            'jsonData' => $matchesJson
        ]), $data->getIdHidden(), true);

        return true;
    }

    /**
     * @param ProblemFinal $problem
     * @return ArrayHash
     * @throws \App\Exceptions\EquationException
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function evaluate(ProblemFinal $problem): ArrayHash
    {
        $standardized = $this->standardize($problem->getBody());
        $a = $this->getDiscriminantA($standardized, $problem->getVariable());
        $b = $this->getDiscriminantB($standardized, $problem->getVariable());
        /*$a = $this->stringsHelper::trim($a);
        $b = $this->stringsHelper::trim($b);*/
        $discriminant = $this->getDiscriminantExpression($standardized, $problem->getVariable());
        $discriminant = $this->evaluateExpression($discriminant);

        $b = $this->evaluateExpression($b);
        $a = $this->evaluateExpression($a);

        if($discriminant > 0){
            $res1 = ((-$b) + sqrt($discriminant)) / (2*$a);
            $res2 = ((-$b) - sqrt($discriminant)) / (2*$a);
            //bdump(sqrt($discriminant));
            $res = [
                'type' => 'double',
                $problem->getVariable() . '_1' => $res1,
                $problem->getVariable() . '_2' => $res2
            ];
        }
        else if((int) $discriminant === 0){
            $res1 = ((-$b) + sqrt($discriminant)) / (2*$a);
            $res = [
                'type' => 'single',
                $problem->getVariable() => $res1
            ];
        }
        else{
            $res = [
                'type' => 'complex',
                $problem->getVariable() => 'complex'
            ];
        }

        return ArrayHash::from($res);
    }

    /**
     * @param QuadraticEquationTemplateNP $data
     * @return bool
     * @throws \App\Exceptions\EntityException
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Nette\Utils\JsonException
     */
    public function validateDiscriminantCond(QuadraticEquationTemplateNP $data): bool
    {
        //bdump('VALIDATE DISCRIMINANT CONDITION');
        $discriminant = $this->getDiscriminantExpression($data->getStandardized(), $data->getVariable());
        $data->setDiscriminant($discriminant);
        $data->setConditionValidateItem('discriminant');

        bdump($data);

        $matches = $this->conditionService->findConditionsMatches([
            $this->constHelper::DISCRIMINANT => [
                $data->getConditionAccessor() => [
                    'data' => $data
                ]
            ]
        ]);

        if (!$matches) {
            return false;
        }

        $jsonData = Json::encode($matches);
        $this->templateJsonDataFunctionality->create(ArrayHash::from([
            'jsonData' => $jsonData
        ]), $data->getIdHidden());

        return true;
    }
}
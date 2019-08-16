<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.7.19
 * Time: 22:21
 */

namespace App\Plugins;

use App\Arguments\EquationValidateArgument;
use App\Model\Entity\ProblemFinal;
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
     * @param string $variable
     * @return mixed
     */
    public static function getRegExp(string $variable): string
    {
        return '('
            . self::RE_EQUATION_SYMBOLS
            . '|'
            . self::RE_LOGARITHM
            . ')*'
            . $variable . '\^2'
            . '('
            . '([\dp' . $variable . '\+\-\*\(\)\/\^])'
            . '|'
            . self::RE_LOGARITHM
            . ')*';
    }

    /**
     * @param string $expression
     * @param string $variable
     * @return false|string
     */
    protected function getDiscriminantA(string $expression, string $variable)
    {
        $aExp = Strings::before($expression, $variable . '^2');
        if($aExp === ''){
            return '1';
        }
        bdump('A EXPRESSION');
        bdump($aExp);
        return $aExp;
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
    protected function getDiscriminantB(string $standardized, string $variable)
    {
        bdump('GET DISCRIMINANT B');
        $bExp = Strings::after($standardized, $variable . '^2');
        $bExpEnd = Strings::indexOf($bExp, $variable);
        $bExp = Strings::substring($bExp, 0, $bExpEnd + 1);
        if($bExp === ' '){
            return '0';
        }
        $bExp = $this->newtonApiClient->simplify($bExp);
        if($bExp === 'x'){
            return '1';
        }
        if($bExp === '-x'){
            return '(-1)';
        }
        $bExp = Strings::replace($bExp, '~' . $variable . '~', '');
        $bExp = Strings::trim($bExp);
        return $this->stringsHelper::wrap($bExp);
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
    protected function getDiscriminantC(string $expression, string $variable)
    {
        bdump('GET DISCRIMINANT C');
        $cExp = Strings::after($expression, $variable, 2);
        if(!$cExp){
            $cExp = Strings::after($expression, $variable . '^2');
            if($cExp === '' || Strings::contains($cExp, $variable)){
                return '0';
            }
        }
        bdump($cExp);
        $cExp = $this->newtonApiClient->simplify($cExp);
        bdump($cExp);
        return $this->stringsHelper::wrap($cExp);
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
        return $this->getDiscriminantB($standardized, $variable) . '^2' . ' - 4 * ' . $this->getDiscriminantA($standardized, $variable) . ' * ' . $this->getDiscriminantC($standardized, $variable);
    }

    /**
     * @param EquationValidateArgument $data
     * @return bool
     */
    public function validateType(EquationValidateArgument $data): bool
    {
        bdump('VALIDATE QUADRATIC EQUATION');

        // Remove all the spaces
        $standardized = $this->stringsHelper::removeWhiteSpaces($data->standardized);

        // Match string against the quadratic expression regexp
        $matches = Strings::match($standardized, '~' . self::getRegExp($data->variable) . '~');

        // Check if the whole expression was matched
        return $matches[0] === $standardized;
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
            bdump(sqrt($discriminant));
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
     * @param int $accessor
     * @param string $standardized
     * @param string $variable
     * @param ArrayHash $parametersInfo
     * @param null $problemId
     * @return bool
     * @throws \App\Exceptions\EntityException
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Nette\Utils\JsonException
     */
    public function validateDiscriminantCond(int $accessor, string $standardized, string $variable, ArrayHash $parametersInfo, $problemId = null): bool
    {
        $discriminantExp = $this->getDiscriminantExpression($standardized, $variable);

        $matches = $this->conditionService->findConditionsMatches([
            $this->constHelper::DISCRIMINANT => [
                $accessor => [
                    'parametersInfo' => $parametersInfo,
                    'data' => $discriminantExp
                ]
            ]
        ]);

        if (!$matches) {
            return false;
        }

        $jsonData = Json::encode($matches);
        $this->templateJsonDataFunctionality->create(ArrayHash::from([
            'jsonData' => $jsonData
        ]), $problemId);

        return true;
    }
}
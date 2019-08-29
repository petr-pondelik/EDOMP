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
     * @param string $variable
     * @return mixed
     */
    public static function getRegExp(string $variable): string
    {
        return '('
            . '('
            . self::RE_EQUATION_SYMBOLS
            . '|'
            . self::RE_LOGARITHM
            . ')*'
            . $variable . '\^\d'
            . ')*'
            . '('
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
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getDiscriminantA(string $expression, string $variable)
    {
        bdump('GET DISCRIMINANT A');
        $aExp = Strings::before($expression, $variable . '^2');
        if($aExp === ''){
            return '1';
        }
        if($aExp === '-'){
            return '(-1)';
        }
        if(Strings::contains($aExp, $variable . '^3')){
            $aExp = Strings::after($aExp, $variable . '^3');
        }
        $aExp = $this->newtonApiClient->simplify($aExp);
        return $this->stringsHelper::wrap($aExp);
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
        bdump($bExp);
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
        $cExp = Strings::after($expression, ' ' . $variable . ' ');
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
        bdump(self::getRegExp($data->getVariable()));

        // Match string against the quadratic expression regexp
        $matches = Strings::match($standardized, '~' . self::getRegExp($data->getVariable()) . '~');

        bdump($matches);

        // Check if the whole expression was matched
        if($matches[0] !== $standardized){
            return false;
        }

        bdump('VARIABLE COEFFICIENTS');
        $variableCoefficients = Strings::matchAll($standardized, '~' . self::RE_VARIABLE_COEFFICIENT . '~');
        bdump($variableCoefficients);

        foreach ($variableCoefficients as $variableCoefficient){
            bdump($variableCoefficient);
            if($variableCoefficient[3] > 2){
                if($variableCoefficient[2] === '' || Strings::match($variableCoefficient[2], '~\d+~')){
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

        if(!$matches){
            // TODO: Handle when there are no parameters matches in validation !!!!
            return false;
        }

        bdump($matches);

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
        bdump('VALIDATE DISCRIMINANT CONDITION');
        $discriminant = $this->getDiscriminantExpression($data->getStandardized(), $data->getVariable());
        $data->setDiscriminant($discriminant);
        $data->setConditionValidateItem('discriminant');

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
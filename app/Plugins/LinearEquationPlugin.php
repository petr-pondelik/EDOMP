<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.7.19
 * Time: 21:07
 */

namespace App\Plugins;

use App\Exceptions\ProblemTemplateException;
use App\Model\NonPersistent\Entity\LinearEquationTemplateNP;
use App\Model\Persistent\Entity\ProblemFinal;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;
use Nette\Utils\Strings;

/**
 * Class LinearEquationPlugin
 * @package App\Plugins
 */
class LinearEquationPlugin extends EquationPlugin
{
    /**
     * @param LinearEquationTemplateNP $data
     * @return bool
     * @throws ProblemTemplateException
     * @throws \App\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     */
    public function validateType(LinearEquationTemplateNP $data): bool
    {
        bdump('VALIDATE LINEAR EQUATION');

        bdump($data->getStandardized());

        // Remove all the spaces
        $standardized = $this->stringsHelper::removeWhiteSpaces($data->getStandardized());

        // Trivial fail case
        if (Strings::match($standardized, '~' . $data->getVariable() . '\^' . '~')) {
            throw new ProblemTemplateException('Ze zadané šablony nelze vygenerovat lineární rovnici.');
        }

//        $parametersInfo = $this->stringsHelper::extractParametersInfo($problemTemplate->body);
        $data->setLinearVariableExpression($this->stringsHelper::getLinearVariableExpresion($data->getStandardized(), $data->getVariable()));

        // Match string against the linear expression regexp
        $matches = Strings::match($standardized, '~' . $this->regularExpressions::getLinearEquationRE($data->getVariable()) . '~');
        bdump($matches);

        // Check if the whole expression was matched
        if($matches[0] !== $standardized){
            throw new ProblemTemplateException('Ze zadané šablony nelze vygenerovat lineární rovnici.');
        }

        //bdump($data);

        try{
            $matches = $this->conditionService->findConditionsMatches([
                $this->constHelper::EXPRESSION_VALIDATION => [
                    $this->constHelper::EXPRESSION_VALID => [
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
            throw new ProblemTemplateException('Ze zadané šablony nelze vygenerovat lineární rovnici.');
        }

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
        //bdump('LINEAR EQUATION EVALUATE');
        $standardized = $this->standardize($problem->getBody());
        $variable = $problem->getVariable();
        $variableExp = $this->stringsHelper::getLinearVariableExpresion($standardized, $variable);

        //bdump($variableExp);

        $res = [
            $variable => $this->evaluateExpression($variableExp)
        ];

        return ArrayHash::from($res);
    }

    /**
     * @param LinearEquationTemplateNP $data
     * @return bool
     * @throws ProblemTemplateException
     * @throws \App\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     */
    public function validateResultCond(LinearEquationTemplateNP $data): bool
    {
        bdump('VALIDATE RESULT COND');
        bdump($data);

        $variableExp = $this->stringsHelper::getLinearVariableExpresion($data->getStandardized(), $data->getVariable());
        $data->setLinearVariableExpression($variableExp);
        $data->setConditionValidateItem('linearVariableExpression');

        bdump($data);

        try {
            $matches = $this->conditionService->findConditionsMatches([
                $this->constHelper::RESULT => [
                    $data->getConditionAccessor() => [
                        'data' => $data
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            throw new ProblemTemplateException('Zadán chybný formát šablony.');
        }

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
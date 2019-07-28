<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.7.19
 * Time: 21:07
 */

namespace App\Plugins;

use App\Arguments\EquationValidateArgument;
use App\Exceptions\ProblemTemplateFormatException;
use App\Model\Entity\ProblemFinal;
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
     * @param string $variable
     * @return string
     */
    public static function getRegExp(string $variable): string
    {
        // Enable all basic equation symbols, logarithm sequence and equation variable
        return '('
            . self::RE_EQUATION_SYMBOLS
            . '|'
            . self::RE_LOGARITHM
            . ')*'
            . $variable
            . '('
            . self::RE_EQUATION_SYMBOLS
            . '|'
            . self::RE_LOGARITHM
            . ')*';
    }

    /**
     * @param EquationValidateArgument $data
     * @return bool
     */
    public function validate(EquationValidateArgument $data): bool
    {
        bdump('VALIDATE LINEAR EQUATION');

        // Remove all the spaces
        $standardized = $this->stringsHelper::removeWhiteSpaces($data->standardized);

        // Trivial fail case
        if (Strings::match($standardized, '~' . $data->variable . '\^' . '~')) {
            return false;
        }

        // Match string against the linear expression regexp
        $matches = Strings::match($standardized, '~' . self::getRegExp($data->variable) . '~');

        // Check if the whole expression was matched
        return $matches[0] === $standardized;
    }

    /**
     * @param ProblemFinal $problem
     * @return ArrayHash
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function evaluate(ProblemFinal $problem): ArrayHash
    {
        bdump('LINEAR EQUATION EVALUATE');
        $standardized = $this->standardize($problem->getBody());
        $variable = $problem->getVariable();
        $variableExp = $this->stringsHelper::getLinearVariableExpresion($standardized, $variable);

        bdump($variableExp);

        $res = [
            $variable => $this->evaluateExpression($variableExp)
        ];

        return ArrayHash::from($res);
    }

    /**
     * @param int $accessor
     * @param string $standardized
     * @param string $variable
     * @param ArrayHash $parametersInfo
     * @param null $problemId
     * @return bool
     * @throws ProblemTemplateFormatException
     * @throws \Nette\Utils\JsonException
     */
    public function validateResultCond(int $accessor, string $standardized, string $variable, ArrayHash $parametersInfo, $problemId = null): bool
    {
        $variableExp = $this->stringsHelper::getLinearVariableExpresion($standardized, $variable);

        try {
            $matches = $this->conditionService->findConditionsMatches([
                $this->constHelper::RESULT => [
                    $accessor => [
                        'parametersInfo' => $parametersInfo,
                        'data' => $variableExp
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            throw new ProblemTemplateFormatException('Zadán chybný formát šablony.');
        }

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
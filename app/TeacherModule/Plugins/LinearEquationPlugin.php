<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.7.19
 * Time: 21:07
 */

namespace App\TeacherModule\Plugins;

use App\TeacherModule\Exceptions\ProblemTemplateException;
use App\CoreModule\Helpers\ConstHelper;
use App\TeacherModule\Services\LatexParser;
use App\CoreModule\Helpers\RegularExpressions;
use App\CoreModule\Helpers\StringsHelper;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
use App\CoreModule\Model\Persistent\Functionality\ProblemFinal\LinearEquationFinalFunctionality;
use App\CoreModule\Model\Persistent\Functionality\TemplateJsonDataFunctionality;
use App\TeacherModule\Services\ConditionService;
use App\TeacherModule\Services\MathService;
use App\TeacherModule\Model\NonPersistent\Entity\LinearEquationTemplateNP;
use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\TeacherModule\Services\NewtonApiClient;
use App\TeacherModule\Services\ProblemGenerator;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;
use Nette\Utils\Strings;

/**
 * Class LinearEquationPlugin
 * @package App\TeacherModule\Plugins
 */
final class LinearEquationPlugin extends EquationPlugin
{
    /**
     * LinearEquationPlugin constructor.
     * @param NewtonApiClient $newtonApiClient
     * @param MathService $mathService
     * @param ConditionService $conditionService
     * @param ProblemGenerator $problemGenerator
     * @param TemplateJsonDataFunctionality $templateJsonDataFunctionality
     * @param LatexParser $latexParser
     * @param StringsHelper $stringsHelper
     * @param ConstHelper $constHelper
     * @param RegularExpressions $regularExpressions
     * @param LinearEquationFinalFunctionality $linearEquationFinalFunctionality
     */
    public function __construct
    (
        NewtonApiClient $newtonApiClient, MathService $mathService, ConditionService $conditionService,
        ProblemGenerator $problemGenerator, TemplateJsonDataFunctionality $templateJsonDataFunctionality,
        LatexParser $latexParser, StringsHelper $stringsHelper,
        ConstHelper $constHelper, RegularExpressions $regularExpressions,
        LinearEquationFinalFunctionality $linearEquationFinalFunctionality
    )
    {
        parent::__construct($newtonApiClient, $mathService, $conditionService, $problemGenerator, $templateJsonDataFunctionality, $latexParser, $stringsHelper, $constHelper, $regularExpressions);
        $this->functionality = $linearEquationFinalFunctionality;
    }

    /**
     * @param ProblemTemplateNP $data
     * @return bool
     * @throws ProblemTemplateException
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     */
    public function validateType(ProblemTemplateNP $data): bool
    {
        bdump('VALIDATE LINEAR EQUATION');

        // Remove all the spaces
        $standardized = $this->stringsHelper::removeWhiteSpaces($data->getStandardized());

        // Trivial fail case
        if (Strings::match($standardized, '~' . $data->getVariable() . '\^' . '~')) {
            throw new ProblemTemplateException('Ze zadané šablony nelze vygenerovat lineární rovnici.');
        }

        $data->setLinearVariableExpression($this->stringsHelper::getLinearVariableExpresion($data->getStandardized(), $data->getVariable()));

        // Match string against the linear expression regexp
        $matches = Strings::match($standardized, '~' . $this->regularExpressions::getLinearEquationRE($data->getVariable()) . '~');

        // Check if the whole expression was matched
        if ($matches[0] !== $standardized) {
            throw new ProblemTemplateException('Ze zadané šablony nelze vygenerovat lineární rovnici.');
        }

        try{
            $matches = $this->conditionService->findConditionsMatches([
                $this->constHelper::EXPRESSION_VALIDATION => [
                    $this->constHelper::EXPRESSION_VALID => [
                        'data' => $data
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            bdump($e);
            throw new ProblemTemplateException('Zadán nepodporovaný formát šablony.');
        }

        if(!$matches){
            // When there are no parameters matches in validation !!!!
            throw new ProblemTemplateException('Ze zadané šablony nelze vygenerovat lineární rovnici.');
        }

        $matchesJson = Json::encode($matches);
        $this->templateJsonDataFunctionality->create(ArrayHash::from([
            'jsonData' => $matchesJson
        ]), true, $data->getIdHidden());

        return true;
    }

    /**
     * @param ProblemFinal $problem
     * @return ArrayHash
     * @throws \App\TeacherModule\Exceptions\EquationException
     * @throws \App\TeacherModule\Exceptions\NewtonApiException
     * @throws \App\TeacherModule\Exceptions\NewtonApiRequestException
     * @throws \App\TeacherModule\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function evaluate(ProblemFinal $problem): ArrayHash
    {
        bdump('LINEAR EQUATION EVALUATE');

        $standardized = $this->standardizeFinal($problem->getBody());
        $variable = $problem->getVariable();
        $variableExp = $this->stringsHelper::getLinearVariableExpresion($standardized, $variable);

        $res = ArrayHash::from([ $variable => $this->mathService->evaluateExpression($variableExp) ]);
        $this->functionality->storeResult($problem->getId(), $res);

        return $res;
    }

    /**
     * @param LinearEquationTemplateNP $data
     * @return bool
     * @throws ProblemTemplateException
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     */
    public function validateResultCond(LinearEquationTemplateNP $data): bool
    {
        bdump('VALIDATE RESULT COND');
        $variableExp = $this->stringsHelper::getLinearVariableExpresion($data->getStandardized(), $data->getVariable());
        $data->setLinearVariableExpression($variableExp);
        $data->setConditionValidateItem('linearVariableExpression');

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
        ]), true, $data->getIdHidden(), $data->getConditionType());

        return true;
    }
}
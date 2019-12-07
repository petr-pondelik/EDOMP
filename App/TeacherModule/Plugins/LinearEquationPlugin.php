<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.7.19
 * Time: 21:07
 */

namespace App\TeacherModule\Plugins;

use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\LinearEquationTemplate;
use App\TeacherModule\Exceptions\ProblemTemplateException;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal;
use App\TeacherModule\Model\NonPersistent\Entity\LinearEquationTemplateNP;
use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;
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
     * @param ProblemTemplateNP $data
     * @return bool
     * @throws ProblemTemplateException
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function validateType(ProblemTemplateNP $data): bool
    {
        bdump('VALIDATE LINEAR EQUATION');

        /**
         * @var LinearEquationTemplateNP $data
         */

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
                $this->constHelper::LINEAR_EQUATION_VALIDATION => [
                    $this->constHelper::LINEAR_EQUATION_VALID => [
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
        ]), true, $data->getId());

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
     * @throws \Exception
     */
    public function evaluate(ProblemFinal $problem): ArrayHash
    {
        bdump('LINEAR EQUATION EVALUATE');

        /**
         * @var LinearEquationTemplate $template
         */
        $template = $problem->getProblemTemplate();

        $standardized = $this->standardizeFinal($problem->getBody());
        $variable = $template->getVariable();
        $variableExp = $this->stringsHelper::getLinearVariableExpresion($standardized, $variable);

        $res = ArrayHash::from([ $variable => $this->mathService->evaluateExpression($variableExp) ]);
        $this->problemFinalFunctionality->storeResult($problem->getId(), $res);

        return $res;
    }

    /**
     * @param LinearEquationTemplateNP $data
     * @return bool
     * @throws ProblemTemplateException
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     * @throws \Doctrine\ORM\EntityNotFoundException
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
        ]), true, $data->getId(), $data->getConditionType());

        return true;
    }
}
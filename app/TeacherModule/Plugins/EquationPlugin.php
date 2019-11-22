<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.7.19
 * Time: 21:23
 */

namespace App\TeacherModule\Plugins;

use App\TeacherModule\Exceptions\NewtonApiSyntaxException;
use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;
use Nette\Utils\ArrayHash;

/**
 * Class EquationPlugin
 * @package App\TeacherModule\Plugins
 */
abstract class EquationPlugin extends ProblemPlugin
{
    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return ProblemTemplateNP
     * @throws \App\TeacherModule\Exceptions\EquationException
     * @throws \App\TeacherModule\Exceptions\NewtonApiException
     * @throws \App\TeacherModule\Exceptions\NewtonApiRequestException
     * @throws \App\TeacherModule\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function preprocess(ProblemTemplateNP $problemTemplate): ProblemTemplateNP
    {
        bdump('PREPROCESS EQUATION');
        $expression = $this->latexParser::parse($problemTemplate->getBody());
        $parameterized = $this->parameterParser::parse($expression);
        $problemTemplate->setExpression($parameterized->expression);
        $sides = $this->stringsHelper::getEquationSides($parameterized->expression);
        $expression = $this->stringsHelper::mergeEqSides($sides);
        $expression = $this->newtonApiClient->simplify($expression);

        $problemTemplate->setStandardized($expression);
        $problemTemplate = $this->mathService->processVariableFractions($problemTemplate);

        return $problemTemplate;
    }

    /**
     * @param string $expression
     * @return string
     * @throws \App\TeacherModule\Exceptions\EquationException
     * @throws \App\TeacherModule\Exceptions\NewtonApiException
     * @throws \App\TeacherModule\Exceptions\NewtonApiRequestException
     * @throws \App\TeacherModule\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function standardizeFinal(string $expression): string
    {
        bdump('STANDARDIZE EQUATION');
        $expression = $this->latexParser::parse($expression);
        $parameterized = $this->parameterParser::parse($expression);
        $sides = $this->stringsHelper::getEquationSides($parameterized->expression);
        $sides->left = $this->newtonApiClient->simplify($sides->left);
        $sides->right = $this->newtonApiClient->simplify($sides->right);
        $expression = $this->stringsHelper::mergeEqSides($sides);
        $expression = $this->newtonApiClient->simplify($expression);
        return $expression;
    }

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return int
     * @throws \App\TeacherModule\Exceptions\EquationException
     * @throws \App\TeacherModule\Exceptions\InvalidParameterException
     * @throws \App\TeacherModule\Exceptions\NewtonApiException
     * @throws \App\TeacherModule\Exceptions\NewtonApiRequestException
     * @throws \App\TeacherModule\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validateBody(ProblemTemplateNP $problemTemplate): int
    {
        bdump('VALIDATE BODY');
        if(!$this->latexParser::latexWrapped($problemTemplate->getBody())){
            return 1;
        }

        $parsed = $this->latexParser::parse($problemTemplate->getBody());

        $this->validateParameters($problemTemplate->getBody());
        $split = $this->parameterParser::splitByParameters($parsed);

        if (empty($problemTemplate->getVariable()) || !$this->stringsHelper::containsVariable($split, $problemTemplate->getVariable())) {
            return 2;
        }

        $parametrized = $this->parameterParser::parse($parsed);

        try {
            $expression = $this->stringsHelper::mergeEqSides($this->stringsHelper::getEquationSides($parametrized->expression));
            $this->newtonApiClient->simplify($expression);
        } catch (NewtonApiSyntaxException $e) {
            bdump($e);
            return 3;
        }

        return -1;
    }

    /**
     * @param ProblemTemplate $problemTemplate
     * @param array|null $usedMatchesInx
     * @return ArrayHash
     * @throws \App\TeacherModule\Exceptions\GeneratorException
     * @throws \Nette\Utils\JsonException
     */
    public function constructFinalData(ProblemTemplate $problemTemplate, ?array $usedMatchesInx): ArrayHash
    {
        $finalData = parent::constructFinalData($problemTemplate, $usedMatchesInx);
        $finalData->variable = $problemTemplate->getVariable();
        return $finalData;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.7.19
 * Time: 21:23
 */

namespace App\Plugins;

use App\Exceptions\NewtonApiSyntaxException;
use App\Model\NonPersistent\Entity\ProblemTemplateNP;

/**
 * Class EquationPlugin
 * @package App\Plugins
 */
abstract class EquationPlugin extends ProblemPlugin
{
    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return ProblemTemplateNP
     * @throws \App\Exceptions\EquationException
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function standardize(ProblemTemplateNP $problemTemplate): ProblemTemplateNP
    {
        bdump('STANDARDIZE EQUATION');

        $expression = $this->latexHelper::parseLatex($problemTemplate->getBody());
        $parameterized = $this->stringsHelper::getParametrized($expression);
        bdump($parameterized);
        $problemTemplate->setExpression($parameterized->expression);
        $sides = $this->stringsHelper::getEquationSides($parameterized->expression);
        $expression = $this->stringsHelper::mergeEqSides($sides);
        bdump($expression);
        $expression = $this->newtonApiClient->simplify($expression);

        bdump('BEFORE VAR FRACTIONS CHECK');
        bdump($expression);

        $problemTemplate->setStandardized($expression);
        $problemTemplate = $this->mathService->processVariableFractions($problemTemplate);

        bdump('STANDARDIZE RESULT');
        bdump($problemTemplate);
        return $problemTemplate;
    }

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return int
     * @throws \App\Exceptions\EquationException
     * @throws \App\Exceptions\InvalidParameterException
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validateBody(ProblemTemplateNP $problemTemplate): int
    {
        bdump('VALIDATE BODY');
        if(!$this->latexHelper::latexWrapped($problemTemplate->getBody())){
            return 1;
        }

        $parsed = $this->latexHelper::parseLatex($problemTemplate->getBody());

        $this->validateParameters($problemTemplate->getBody());
        $split = $this->stringsHelper::splitByParameters($parsed);

        if (empty($problemTemplate->getVariable()) || !$this->stringsHelper::containsVariable($split, $problemTemplate->getVariable())) {
            return 2;
        }

        $parametrized = $this->stringsHelper::getParametrized($parsed);

        try {
            $expression = $this->stringsHelper::mergeEqSides($this->stringsHelper::getEquationSides($parametrized->expression));
            $this->newtonApiClient->simplify($expression);
        } catch (NewtonApiSyntaxException $e) {
            bdump($e);
            return 3;
        }

        return -1;
    }
}
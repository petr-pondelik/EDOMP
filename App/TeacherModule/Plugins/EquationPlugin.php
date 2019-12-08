<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.7.19
 * Time: 21:23
 */

namespace App\TeacherModule\Plugins;

use App\TeacherModule\Exceptions\NewtonApiSyntaxException;
use App\TeacherModule\Model\NonPersistent\Entity\EquationTemplateNP;
use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;

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
        /** @var EquationTemplateNP $problemTemplate */
        bdump('PREPROCESS EQUATION');

        // Parse latex and XML parameters
        $expression = $this->latexParser::parse($problemTemplate->getBody());
        $parameterized = $this->parameterParser::parse($expression);
        $problemTemplate->setExpression($parameterized->expression);

        // Finish standardization: move equation on left side with 0 remaining on the right side
        $sides = $this->mathService::getEquationSides($parameterized->expression);
        $expression = $this->mathService::mergeEqSides($sides);
        $expression = $this->newtonApiClient->simplify($expression);
        $problemTemplate->setStandardized($expression);

        // Process variable fractions, if exist
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
        $sides = $this->mathService::getEquationSides($parameterized->expression);
        $sides->left = $this->newtonApiClient->simplify($sides->left);
        $sides->right = $this->newtonApiClient->simplify($sides->right);
        $expression = $this->mathService::mergeEqSides($sides);
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
        /** @var EquationTemplateNP $problemTemplate */

        if(!$this->latexParser::latexWrapped($problemTemplate->getBody())){
            return 1;
        }

        $parsed = $this->latexParser::parse($problemTemplate->getBody());

        $this->validateParameters($problemTemplate->getBody());
        $split = $this->parameterParser::splitByParameters($parsed);

        if (empty($problemTemplate->getVariable()) || !$this->mathService::containsVariable($split, $problemTemplate->getVariable())) {
            return 2;
        }

        $parametrized = $this->parameterParser::parse($parsed);

        try {
            $expression = $this->mathService::mergeEqSides($this->mathService::getEquationSides($parametrized->expression));
            $this->newtonApiClient->simplify($expression);
        } catch (NewtonApiSyntaxException $e) {
            bdump($e);
            return 3;
        }

        return -1;
    }
}
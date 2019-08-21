<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.7.19
 * Time: 21:23
 */

namespace App\Plugins;

use App\Arguments\BodyArgument;
use App\Exceptions\NewtonApiSyntaxException;
use Nette\Utils\Strings;

/**
 * Class EquationPlugin
 * @package App\Plugins
 */
abstract class EquationPlugin extends ProblemPlugin
{
    /**
     * @param string $expression
     * @return string
     * @throws \App\Exceptions\EquationException
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function standardize(string $expression): string
    {
        bdump('STANDARDIZE EQUATION');

        $expression = $this->latexHelper::parseLatex($expression);
        $parameterized = $this->stringsHelper::getParametrized($expression);

        $sides = $this->stringsHelper::getEquationSides($parameterized->expression);
        $expression = $this->stringsHelper::mergeEqSides($sides);

        $expression = $this->newtonApiClient->simplify($expression);

        bdump($expression);



        $varDividers = Strings::matchAll($expression, '(\+|\-|)([x\d\sp\^]+)\/\s*(\([\-\+\s\(\)\dx]+\))');

        bdump($varDividers);

        return $expression;
    }

    /**
     * @param BodyArgument $argument
     * @return int
     * @throws \App\Exceptions\InvalidParameterException
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validateBody(BodyArgument $argument): int
    {
        bdump('VALIDATE BODY');
        if(!$this->latexHelper::latexWrapped($argument->body)){
            return 1;
        }
        $parsed = $this->latexHelper::parseLatex($argument->body);

        $this->validateParameters($argument->body);
        $split = $this->stringsHelper::splitByParameters($parsed);

        if (empty($argument->variable) || !$this->stringsHelper::containsVariable($split, $argument->variable)) {
            return 2;
        }

        $parametrized = $this->stringsHelper::getParametrized($parsed);

        try {
            $this->newtonApiClient->simplify($parametrized->expression);
        } catch (NewtonApiSyntaxException $e) {
            return 3;
        }

        return -1;
    }
}
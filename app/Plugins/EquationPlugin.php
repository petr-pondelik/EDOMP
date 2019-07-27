<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.7.19
 * Time: 21:23
 */

namespace App\Plugins;

/**
 * Class EquationPlugin
 * @package App\Plugins
 */
abstract class EquationPlugin extends ProblemPlugin
{
    /**
     * @param string $expression
     * @return string
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
        $sides->left = $this->newtonApiClient->simplify($sides->left);
        $sides->right = $this->newtonApiClient->simplify($sides->right);
        $expression = $this->stringsHelper::mergeEqSides($sides);
        $expression = $this->newtonApiClient->simplify($expression);
        bdump($expression);
        return $expression;
    }
}
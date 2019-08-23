<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.7.19
 * Time: 21:23
 */

namespace App\Plugins;

use App\Exceptions\NewtonApiSyntaxException;
use App\Model\NonPersistent\ProblemTemplateNP;
use Nette\Utils\Strings;

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

        $expression = $this->latexHelper::parseLatex($problemTemplate->body);
        $parameterized = $this->stringsHelper::getParametrized($expression);
        $problemTemplate->expression = $parameterized->expression;
        $sides = $this->stringsHelper::getEquationSides($parameterized->expression);
        $expression = $this->stringsHelper::mergeEqSides($sides);
        $expression = $this->newtonApiClient->simplify($expression);

        $varDividers = Strings::matchAll($expression, '~(\+|\-|)([x\d\sp\^]+)\/\s*(\([\-\+\s\(\)\dx]+\))~');

        if($varDividers){
            bdump('WITH VAR DIVIDERS');
            $this->variableDividers->setData($expression, $varDividers);
            $expression = $this->variableDividers->getMultiplied();
            bdump($expression);
            $expression = $this->newtonApiClient->simplify($expression);
        }
        else{
            bdump('WITHOUT VAR DIVIDERS');
        }

        $problemTemplate->standardized = $this->stringsHelper::fillMultipliers($expression);

        return $problemTemplate;
    }

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return int
     * @throws \App\Exceptions\InvalidParameterException
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validateBody(ProblemTemplateNP $problemTemplate): int
    {
        bdump('VALIDATE BODY');
        if(!$this->latexHelper::latexWrapped($problemTemplate->body)){
            return 1;
        }
        $parsed = $this->latexHelper::parseLatex($problemTemplate->body);

        $this->validateParameters($problemTemplate->body);
        $split = $this->stringsHelper::splitByParameters($parsed);

        if (empty($problemTemplate->variable) || !$this->stringsHelper::containsVariable($split, $problemTemplate->variable)) {
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
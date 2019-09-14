<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.7.19
 * Time: 0:00
 */

namespace App\Plugins;

use App\Exceptions\NewtonApiSyntaxException;
use App\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
use App\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class SequencePlugin
 * @package App\Plugins
 */
abstract class SequencePlugin extends ProblemPlugin
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
        bdump('STANDARDIZE SEQUENCE');

        $expression = $this->latexHelper::parseLatex($problemTemplate->getBody());
        $problemTemplate->setExpression($expression);
        $parametrized = $this->stringsHelper::getParametrized($expression);
        $sides = $this->stringsHelper::getEquationSides($parametrized->expression);
        $expression = $this->newtonApiClient->simplify($sides->right);
        $problemTemplate->setStandardized($expression);

        return $problemTemplate;
    }

    /**
     * @param string $expression
     * @return string
     * @throws \App\Exceptions\EquationException
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function standardizeFinal(string $expression): string
    {
        bdump('STANDARDIZE SEQUENCE');

        $expression = $this->latexHelper::parseLatex($expression);
        $parametrized = $this->stringsHelper::getParametrized($expression);
        $sides = $this->stringsHelper::getEquationSides($parametrized->expression);
        $expression = $this->newtonApiClient->simplify($sides->right);

        return $expression;
    }

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return bool
     */
    public function validateType(ProblemTemplateNP $problemTemplate): bool
    {
        bdump('VALIDATE SEQUENCE');
        bdump($problemTemplate);
        if(!Strings::match($problemTemplate->getExpression(), '~' . $this->regularExpressions::getSequenceRE($problemTemplate->getIndexVariable()) . '~')){
            return false;
        }
        return true;
    }

    /**
     * @param ProblemFinal $problem
     * @return ArrayHash
     * @throws \App\Exceptions\EquationException
     */
    public function evaluate(ProblemFinal $problem): ArrayHash
    {
        $parsed = $this->latexHelper::parseLatex($problem->getBody());
        $variable = $problem->getVariable();

        $sides = $this->stringsHelper::getEquationSides($parsed, false);
        $seqName = $this->stringsHelper::extractSequenceName($sides->left);

        //$problem = $this->problemFinalRepository->find($problem->getId());
        $firstN = $problem->getFirstN();
        $res = [];

        $sides->right = $this->stringsHelper::fillMultipliers($sides->right, $variable);

        for($i = 1; $i <= $firstN; $i++){
            $res[$seqName . '_{' . $i . '}'] = $this->mathService->evaluateExpression(
                $this->stringsHelper::passValues($sides->right, [
                    $variable => $i
                ])
            );
        }

        return ArrayHash::from([
            'seqName' => $seqName,
            'res' => $res
        ]);
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
        if(!$this->latexHelper::latexWrapped($problemTemplate->getBody())){
            return 1;
        }
        $parsed = $this->latexHelper::parseLatex($problemTemplate->getBody());

        $this->validateParameters($problemTemplate->getBody());
        $split = $this->stringsHelper::splitByParameters($parsed);

        if (empty($problemTemplate->getIndexVariable()) || !$this->stringsHelper::containsVariable($split, $problemTemplate->getIndexVariable())) {
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

    /**
     * @param ProblemTemplate $problemTemplate
     * @return ArrayHash
     * @throws \Nette\Utils\JsonException
     */
    public function constructProblemFinalData(ProblemTemplate $problemTemplate): ArrayHash
    {
        $finalData = parent::constructProblemFinalData($problemTemplate);
        $finalData->index = $problemTemplate->getIndexVariable();
        $finalData->firstN = $problemTemplate->getFirstN();
        return $finalData;
    }
}
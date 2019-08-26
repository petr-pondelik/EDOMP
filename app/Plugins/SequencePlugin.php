<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.7.19
 * Time: 0:00
 */

namespace App\Plugins;

use App\Arguments\BodyArgument;
use App\Arguments\SequenceValidateArgument;
use App\Exceptions\NewtonApiSyntaxException;
use App\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\Model\Persistent\Entity\ProblemFinal;
use Nette\Utils\ArrayHash;

/**
 * Class SequencePlugin
 * @package App\Plugins
 */
abstract class SequencePlugin extends ProblemPlugin
{
    /**
     * @param string $variable
     * @return mixed
     */
    public static function getRegExp(string $variable): string
    {
        return '^\s*\w'
            . $variable
            . '\s*=~';
    }

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
        $expression = $this->latexHelper::parseLatex($problemTemplate);
        $parametrized = $this->stringsHelper::getParametrized($expression);
        $sides = $this->stringsHelper::getEquationSides($parametrized->expression);
        $expression = $this->newtonApiClient->simplify($sides->right);
        $problemTemplate->standardized = $expression;
        return $problemTemplate;
    }

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return bool
     */
    public function validateType(ProblemTemplateNP $problemTemplate): bool
    {
        bdump('VALIDATE SEQUENCE');
        if (!$this->stringsHelper::isSequence($this->latexHelper::parseLatex($problemTemplate->expression), $problemTemplate->variable)) {
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
            $res[$seqName . '_{' . $i . '}'] = $this->parser::solve(
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
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.7.19
 * Time: 0:00
 */

namespace App\Plugins;

use App\Arguments\SequenceValidateArgument;
use App\Model\Entity\ProblemFinal;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

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
     * @param string $expression
     * @return string
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function standardize(string $expression): string
    {
        bdump('STANDARDIZE SEQUENCE');
        $expression = $this->latexHelper::parseLatex($expression);
        $parametrized = $this->stringsHelper::getParametrized($expression);
        $sides = $this->stringsHelper::getEquationSides($parametrized->expression);
        $expression = $this->newtonApiClient->simplify($sides->right);
        bdump($expression);
        return $expression;
    }

    /**
     * @param SequenceValidateArgument $data
     * @return bool
     */
    public function validate(SequenceValidateArgument $data): bool
    {
        bdump('VALIDATE SEQUENCE');
        if (!$this->stringsHelper::isSequence($this->latexHelper::parseLatex($data->expression), $data->variable)) {
            return false;
        }
        return true;
    }

    /**
     * @param ProblemFinal $problem
     * @return ArrayHash
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

        $sides->right = Strings::replace($sides->right, '~(\d)(' . $variable . ')~', '$1*$2');
        $sides->right = Strings::replace($sides->right, '~(\d)(' . $variable . ')~', '$1*$2');
        $sides->right = Strings::replace($sides->right, '~(\d)\s*(' . $variable . ')~', '$1*$2');
        $sides->right = Strings::replace($sides->right, '~(\s*\))(' . $variable . ')~', '$1*$2');

        bdump($sides->right);

        for($i = 1; $i <= $firstN; $i++){
            bdump($this->stringsHelper::passValues($sides->right, [
                $variable => $i
            ]));
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
}
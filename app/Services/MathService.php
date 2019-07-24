<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.3.19
 * Time: 23:18
 */

namespace App\Services;

use App\Exceptions\ProblemTemplateFormatException;
use App\Exceptions\StringFormatException;
use App\Helpers\ConstHelper;
use App\Helpers\LatexHelper;
use App\Helpers\StringsHelper;
use App\Model\Entity\ProblemFinal;
use App\Model\Repository\ProblemFinalRepository;
use jlawrence\eos\Parser;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;
use NXP\MathExecutor;

/**
 * Class MathService
 * @package App\Services
 */
class MathService
{
    /**
     * @const bool
     */
    protected const STANDARDIZED = true;

    /**
     * @const bool
     */
    protected const NON_STANDARDIZED = false;

    /**
     * @const bool
     */
    protected const ARITHMETIC = true;

    /**
     * @const bool
     */
    protected const GEOMETRIC = false;

    /**
     * @var NewtonApiClient
     */
    protected $newtonApiClient;

    /**
     * @var ProblemFinalRepository
     */
    protected $problemFinalRepository;

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * @var LatexHelper
     */
    protected $latexHelper;

    /**
     * @var array
     */
    public $evaluate = [];

    /**
     * MathService constructor.
     * @param NewtonApiClient $newtonApiClient
     * @param ProblemFinalRepository $problemFinalRepository
     * @param ConstHelper $constHelper
     * @param StringsHelper $stringsHelper
     * @param LatexHelper $latexHelper
     * @param Parser $parser
     */
    public function __construct
    (
        NewtonApiClient $newtonApiClient,
        ProblemFinalRepository $problemFinalRepository,
        ConstHelper $constHelper, StringsHelper $stringsHelper, LatexHelper $latexHelper,
        Parser $parser
    )
    {
        $this->newtonApiClient = $newtonApiClient;
        $this->problemFinalRepository = $problemFinalRepository;
        $this->parser = $parser;
        $this->constHelper = $constHelper;
        $this->stringsHelper = $stringsHelper;
        $this->latexHelper = $latexHelper;

        $this->evaluate = [

            $this->constHelper::LINEAR_EQ => function(ProblemFinal $problem){
                return $this->evaluateLinearEquation($problem);
            },

            $this->constHelper::QUADRATIC_EQ => function(ProblemFinal $problem){
                return $this->evaluateQuadraticEquation($problem);
            },

            $this->constHelper::ARITHMETIC_SEQ => function(ProblemFinal $problem){
                return $this->evaluateSequence($problem);
            },

            $this->constHelper::GEOMETRIC_SEQ => function(ProblemFinal $problem){
                return $this->evaluateSequence($problem, self::GEOMETRIC);
            }

        ];
    }

    /**
     * @param string $expression
     * @param string $variable
     * @return false|string
     */
    protected function getDiscriminantA(string $expression, string $variable)
    {
        $aExp = Strings::before($expression, $variable . '^2');
        if($aExp === ''){
            return '1';
        }
        bdump('A EXPRESSION');
        bdump($aExp);
        return $aExp;
    }

    /**
     * @param string $standardized
     * @param string $variable
     * @return false|string
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getDiscriminantB(string $standardized, string $variable)
    {
        bdump('GET DISCRIMINANT B');
        $bExp = Strings::after($standardized, $variable . '^2');
        $bExpEnd = Strings::indexOf($bExp, $variable);
        $bExp = Strings::substring($bExp, 0, $bExpEnd + 1);
        if($bExp === ' '){
            return '0';
        }
        $bExp = $this->newtonApiClient->simplify($bExp);
        if($bExp === 'x'){
            return '1';
        }
        if($bExp === '-x'){
            return '(-1)';
        }
        $bExp = Strings::replace($bExp, '~' . $variable . '~', '');
        $bExp = Strings::trim($bExp);
        return $this->stringsHelper::wrap($bExp);
    }

    /**
     * @param string $expression
     * @param string $variable
     * @return false|string
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getDiscriminantC(string $expression, string $variable)
    {
        bdump('GET DISCRIMINANT C');
        $cExp = Strings::after($expression, $variable, 2);
        if(!$cExp){
            $cExp = Strings::after($expression, $variable . '^2');
            if($cExp === '' || Strings::contains($cExp, $variable)){
                return '0';
            }
        }
        bdump($cExp);
        $cExp = $this->newtonApiClient->simplify($cExp);
        bdump($cExp);
        return $this->stringsHelper::wrap($cExp);
    }

    /**
     * @param string $standardized
     * @param string $variable
     * @return string
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDiscriminantExpression(string $standardized, string $variable): string
    {
        return $this->getDiscriminantB($standardized, $variable) . '^2' . ' - 4 * ' . $this->getDiscriminantA($standardized, $variable) . ' * ' . $this->getDiscriminantC($standardized, $variable);
    }

    /**
     * @param string $expression
     * @return number
     */
    public function evaluateExpression(string $expression)
    {
        return $this->parser::solve($expression);
    }

    /**
     * @param string $expression
     * @return string
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function standardizeEquation(string $expression): string
    {
        bdump('STANDARDIZE EQUATION');
        bdump($expression);

        $expression = $this->latexHelper::parseLatex($expression);

        $parameterized = $this->stringsHelper::getParametrized($expression);

        bdump($parameterized);

        $sides = $this->stringsHelper::getEquationSides($parameterized->expression);
//        var_dump($sides);
        $sides->left = $this->newtonApiClient->simplify($sides->left);
        $sides->right = $this->newtonApiClient->simplify($sides->right);

        $expression = $this->stringsHelper::mergeEqSides($sides);
//        var_dump($expression);
        $expression = $this->newtonApiClient->simplify($expression);

        return $expression;
    }

    /**
     * @param string $expression
     * @return string
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function standardizeSequence(string $expression): string
    {
        bdump('STANDARDIZE SEQUENCE');
        bdump($expression);

        $expression = $this->latexHelper::parseLatex($expression);
        bdump($expression);
        $parametrized = $this->stringsHelper::getParametrized($expression);
        $sides = $this->stringsHelper::getEquationSides($parametrized->expression);
        $expression = $this->newtonApiClient->simplify($sides->right);

        bdump($expression);

        return $expression;
    }

    /**
     * @param ProblemFinal $problem
     * @return ArrayHash
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function evaluateLinearEquation(ProblemFinal $problem): ArrayHash
    {
        $standardized = $this->standardizeEquation($problem->getBody());
        $variable = $problem->getVariable();
        $variableExp = $this->stringsHelper::getLinearVariableExpresion($standardized, $variable);

        $res = [
            $variable => $this->evaluateExpression($variableExp)
        ];

        return ArrayHash::from($res);
    }

    /**
     * @param ProblemFinal $problem
     * @return ArrayHash
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function evaluateQuadraticEquation(ProblemFinal $problem): ArrayHash
    {
        $standardized = $this->standardizeEquation($problem->getBody());
        $a = $this->getDiscriminantA($standardized, $problem->getVariable());
        $b = $this->getDiscriminantB($standardized, $problem->getVariable());
        /*$a = $this->stringsHelper::trim($a);
        $b = $this->stringsHelper::trim($b);*/
        $discriminant = $this->getDiscriminantExpression($standardized, $problem->getVariable(), self::STANDARDIZED);
        $discriminant = $this->evaluateExpression($discriminant);

        $b = $this->evaluateExpression($b);
        $a = $this->evaluateExpression($a);

        if($discriminant > 0){
            $res1 = ((-$b) + sqrt($discriminant)) / (2*$a);
            $res2 = ((-$b) - sqrt($discriminant)) / (2*$a);
            bdump(sqrt($discriminant));
            $res = [
                'type' => 'double',
                $problem->getVariable() . '_1' => $res1,
                $problem->getVariable() . '_2' => $res2
            ];
        }
        else if($discriminant === 0){
            $res1 = ((-$b) + sqrt($discriminant)) / (2*$a);
            $res = [
                'type' => 'single',
                $problem->getVariable() => $res1
            ];
        }
        else{
            $res = [
                'type' => 'complex',
                $problem->getVariable() => 'complex'
            ];
        }

        return ArrayHash::from($res);
    }

    /**
     * @param ProblemFinal $problem
     * @param bool $sequenceType
     * @return bool|ArrayHash
     */
    protected function evaluateSequence(ProblemFinal $problem, bool $sequenceType = self::ARITHMETIC)
    {
        $parsed = $this->latexHelper::parseLatex($problem->getBody());
        $variable = $problem->getVariable();

        try{
            $sides = $this->stringsHelper::getEquationSides($parsed, false);
            $seqName = $this->stringsHelper::extractSequenceName($sides->left);
        } catch (StringFormatException $e){
            return false;
        }

        //$problem = $this->problemFinalRepository->find($problem->getId());
        $firstN = $problem->getFirstN();
        $res = [];

        $sides->right = $this->stringsHelper::nxpFormat($sides->right, $problem->getVariable());
        $sides->right = Strings::replace($sides->right, '~(\d)(' . $variable . ')~', '$1*$2');
        $sides->right = Strings::replace($sides->right, '~(\d)(' . $variable . ')~', '$1*$2');
        $sides->right = Strings::replace($sides->right, '~(\s*\))(' . $variable . ')~', '$1*$2');

        for($i = 1; $i <= $firstN; $i++){
            $res[$seqName . '_{' . $i . '}'] = $this->evaluateExpression(
                $this->stringsHelper::passValues($sides->right, [
                    $variable => $i
                ])
            );
        }

        if($sequenceType === self::ARITHMETIC)
        {
            $difference = (string) round($res[$seqName . '_{' . '2}'] - $res[$seqName . '_{' . '1}'], 1);
            $res['Diference'] = $difference;
        }
        else{
            $quotient = (string) round($res[$seqName . '_{' . '2}'] / $res[$seqName . '_{' . '1}'], 1);
            $res['Kvocient'] = $quotient;
        }

        return ArrayHash::from($res);
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.3.19
 * Time: 23:18
 */

namespace App\Services;

use App\Exceptions\StringFormatException;
use App\Helpers\ConstHelper;
use App\Helpers\LatexHelper;
use App\Helpers\StringsHelper;
use App\Model\Entity\ProblemFinal;
use App\Model\Repository\ProblemFinalRepository;
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
     * @var GeneratorService
     */
    protected $generatorService;

    /**
     * @var ProblemFinalRepository
     */
    protected $problemRepository;

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
     * @param GeneratorService $generatorService
     * @param ProblemFinalRepository $problemRepository
     * @param ConstHelper $constHelper
     * @param StringsHelper $stringsHelper
     * @param LatexHelper $latexHelper
     */
    public function __construct
    (
        NewtonApiClient $newtonApiClient,
        GeneratorService $generatorService,
        ProblemFinalRepository $problemRepository,
        ConstHelper $constHelper, StringsHelper $stringsHelper, LatexHelper $latexHelper
    )
    {
        $this->newtonApiClient = $newtonApiClient;
        $this->generatorService = $generatorService;
        $this->problemRepository = $problemRepository;
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
                return $this->evaluateSequence($problem, self::ARITHMETIC);
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
    public function getDiscriminantA(string $expression, string $variable)
    {
        $aExp = Strings::before($expression, $variable . "^2", 1);
        bdump($aExp);
        if($aExp == "")
            return "1";
        return $this->stringsHelper::trim($aExp, $this->stringsHelper::BRACKETS_SIMPLE);
    }

    /**
     * @param string $expression
     * @param string $variable
     * @return false|string
     */
    public function getDiscriminantB(string $expression, string $variable)
    {
        $bExp = Strings::before($expression, $variable, 2);
        $bExp = Strings::after($bExp, $variable . "^2", 1);
        $bExp = $this->stringsHelper::trimOperators($bExp);
        bdump($bExp);
        if($bExp == "")
            return "0";
        return $bExp;
    }

    /**
     * @param string $expression
     * @param string $variable
     * @return false|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDiscriminantC(string $expression, string $variable)
    {
        $cExp = Strings::after($expression, $variable, 2);
        if($cExp == ''){
            $cExp = Strings::after($expression, $variable . '^2', 1);
            if($cExp === '')
                return "0";
        }
        $cExp = $this->newtonApiClient->simplify($cExp);
        bdump('C EXPR:' . $cExp);
        return $this->stringsHelper::wrap($cExp);
    }

    /**
     * @param string $expression
     * @param string $variable
     * @param bool $standardized
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDiscriminantExpression(string $expression, string $variable, bool $standardized = self::NON_STANDARDIZED)
    {
        if(!$standardized)
            $expression = $this->standardizeEquation($expression);
        bdump("CHECK");
        bdump($expression);
        return $this->getDiscriminantB($expression, $variable) . '^2' . ' - 4 * ' . $this->getDiscriminantA($expression, $variable) . ' * ' . $this->getDiscriminantC($expression, $variable);
    }

    /**
     * @param string $expression
     * @return number
     */
    public function evaluateExpression(string $expression)
    {
        $executor = new MathExecutor();
        return $executor->execute($expression);
    }

    /**
     * @param string $expression
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function standardizeEquation(string $expression)
    {
        $expression = $this->latexHelper::parseLatex($expression);

        $parameterized = $this->stringsHelper::getParametrized($expression);
        $parameterized = $this->stringsHelper::newtonFormat($parameterized->expression);

        bdump($parameterized);

        $sides = $this->stringsHelper::getEquationSides($parameterized);
        bdump($sides);
        $sides->left = $this->newtonApiClient->simplify($sides->left);
        $sides->right = $this->newtonApiClient->simplify($sides->right);

        bdump($sides);

        $expression = $this->stringsHelper::mergeEqSides($sides);
        $expression = $this->stringsHelper::newtonFormat($expression);
        $expression = $this->newtonApiClient->simplify($expression);

        return $expression;
    }

    /**
     * @param ProblemFinal $problem
     * @return ArrayHash
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function evaluateLinearEquation(ProblemFinal $problem)
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function evaluateQuadraticEquation(ProblemFinal $problem)
    {
        $standardized = $this->standardizeEquation($problem->getBody());
        $a = $this->getDiscriminantA($standardized, $problem->getVariable());
        $b = $this->getDiscriminantB($standardized, $problem->getVariable());
        $discriminant = $this->getDiscriminantExpression($standardized, $problem->getVariable(), self::STANDARDIZED);
        $discriminant = $this->evaluateExpression($discriminant);

        $res = [];

        if($discriminant > 0){
            $res1 = ((-$b) + sqrt($discriminant)) / (2*$a);
            $res2 = ((-$b) - sqrt($discriminant)) / (2*$a);
            $res = [
                "type" => "double",
                $problem->getVariable() . "_1" => $res1,
                $problem->getVariable() . "_2" => $res2
            ];
        }
        else if($discriminant === 0){
            $res1 = ((-$b) + sqrt($discriminant)) / (2*$a);
            $res = [
                "type" => "single",
                $problem->getVariable() => $res1
            ];
        }
        else{
            $res = [
                "type" => "complex",
                $problem->getVariable() => "complex"
            ];
        }

        return ArrayHash::from($res);
    }

    /**
     * @param ProblemFinal $problem
     * @param bool $sequenceType
     * @return bool|ArrayHash
     */
    public function evaluateSequence(ProblemFinal $problem, bool $sequenceType = self::ARITHMETIC)
    {
        $parsed = $this->latexHelper::parseLatex($problem->getBody());
        $variable = $problem->getVariable();

        try{
            $sides = $this->stringsHelper::getEquationSides($parsed, false);
            $seqName = $this->stringsHelper::extractSequenceName($sides->left);
        } catch (StringFormatException $e){
            return false;
        }

        bdump($seqName);

        $problem = $this->problemRepository->find($problem->getId());
        $firstN = $problem->first_n;
        $res = [];

        for($i = 1; $i <= $firstN; $i++){
            $res[$seqName . "_{" . $i . "}"] = $this->evaluateExpression(
                $this->stringsHelper::passValues($sides->right, [
                    $variable => $i
                ])
            );
        }

        if($sequenceType === self::ARITHMETIC)
        {
            $difference = (string) round($res[$seqName . '_{' . '2}'] - $res[$seqName . '_{' . '1}'], 1);
            $res["Diference"] = $difference;
        }
        else{
            $quotient = (string) round($res[$seqName . '_{' . '2}'] / $res[$seqName . '_{' . '1}'], 1);
            $res["Kvocient"] = $quotient;
        }

        return ArrayHash::from($res);
    }

}
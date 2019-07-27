<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.3.19
 * Time: 23:18
 */

namespace App\Services;

use App\Helpers\ConstHelper;
use App\Helpers\LatexHelper;
use App\Helpers\StringsHelper;
use App\Model\Entity\ProblemFinal;
use App\Model\Repository\ProblemFinalRepository;
use App\Plugins\ArithmeticSequencePlugin;
use App\Plugins\GeometricSequencePlugin;
use App\Plugins\LinearEquationPlugin;
use App\Plugins\QuadraticEquationPlugin;
use jlawrence\eos\Parser;
use Nette\Utils\ArrayHash;

/**
 * Class MathService
 * @package App\Services
 */
class MathService
{
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
     * @var LinearEquationPlugin
     */
    protected $linearEquationPlugin;

    /**
     * @var QuadraticEquationPlugin
     */
    protected $quadraticEquationPlugin;

    /**
     * @var ArithmeticSequencePlugin
     */
    protected $arithmeticSequencePlugin;

    /**
     * @var GeometricSequencePlugin
     */
    protected $geometricSequencePlugin;

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
     * @param LinearEquationPlugin $linearEquationPlugin
     * @param QuadraticEquationPlugin $quadraticEquationPlugin
     * @param ArithmeticSequencePlugin $arithmeticSequencePlugin
     * @param GeometricSequencePlugin $geometricSequencePlugin
     */
    public function __construct
    (
        NewtonApiClient $newtonApiClient,
        ProblemFinalRepository $problemFinalRepository,
        ConstHelper $constHelper, StringsHelper $stringsHelper, LatexHelper $latexHelper,
        Parser $parser,
        LinearEquationPlugin $linearEquationPlugin,
        QuadraticEquationPlugin $quadraticEquationPlugin,
        ArithmeticSequencePlugin $arithmeticSequencePlugin,
        GeometricSequencePlugin $geometricSequencePlugin
    )
    {
        $this->newtonApiClient = $newtonApiClient;
        $this->problemFinalRepository = $problemFinalRepository;
        $this->parser = $parser;
        $this->constHelper = $constHelper;
        $this->stringsHelper = $stringsHelper;
        $this->latexHelper = $latexHelper;
        $this->linearEquationPlugin = $linearEquationPlugin;
        $this->quadraticEquationPlugin = $quadraticEquationPlugin;
        $this->arithmeticSequencePlugin = $arithmeticSequencePlugin;
        $this->geometricSequencePlugin = $geometricSequencePlugin;

        $this->evaluate = [

            $this->constHelper::LINEAR_EQ => function(ProblemFinal $problem){
                return $this->evaluateLinearEquation($problem);
            },

            $this->constHelper::QUADRATIC_EQ => function(ProblemFinal $problem){
                return $this->evaluateQuadraticEquation($problem);
            },

            $this->constHelper::ARITHMETIC_SEQ => function(ProblemFinal $problem){
                return $this->evaluateArithmeticSequence($problem);
            },

            $this->constHelper::GEOMETRIC_SEQ => function(ProblemFinal $problem){
                return $this->evaluateGeometricSequence($problem);
            }

        ];
    }

    /**
     * @param string $expression
     * @return string
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function standardizeLinearEquation(string $expression): string
    {
        return $this->linearEquationPlugin->standardize($expression);
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
        return $this->linearEquationPlugin->evaluate($problem);
    }

    /**
     * @param string $expression
     * @return string
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function standardizeQuadraticEquation(string $expression): string
    {
        return $this->quadraticEquationPlugin->standardize($expression);
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
        return $this->quadraticEquationPlugin->evaluate($problem);
    }

    /**
     * @param string $expression
     * @return string
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function standardizeArithmeticSequence(string $expression): string
    {
        return $this->arithmeticSequencePlugin->standardize($expression);
    }

    /**
     * @param ProblemFinal $problem
     * @return ArrayHash
     */
    public function evaluateArithmeticSequence(ProblemFinal $problem): ArrayHash
    {
        return $this->arithmeticSequencePlugin->evaluate($problem);
    }

    /**
     * @param string $expression
     * @return string
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function standardizeGeometricSequence(string $expression): string
    {
        return $this->geometricSequencePlugin->standardize($expression);
    }

    /**
     * @param ProblemFinal $problem
     * @return ArrayHash
     */
    public function evaluateGeometricSequence(ProblemFinal $problem): ArrayHash
    {
        return $this->geometricSequencePlugin->evaluate($problem);
    }
}
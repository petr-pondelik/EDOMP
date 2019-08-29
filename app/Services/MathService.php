<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 18.8.19
 * Time: 18:57
 */

namespace App\Services;

use App\Helpers\StringsHelper;
use App\Model\NonPersistent\Entity\EquationTemplateNP;
use App\Model\NonPersistent\Entity\LinearEquationTemplateNP;
use App\Model\NonPersistent\Entity\ProblemTemplateNP;
use jlawrence\eos\Parser;

/**
 * Class MathService
 * @package App\Services
 */
class MathService
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var GeneratorService
     */
    protected $generatorService;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * @var VariableFractionService
     */
    public $variableFractionService;

    /**
     * MathService constructor.
     * @param Parser $parser
     * @param GeneratorService $generatorService
     * @param StringsHelper $stringsHelper
     * @param VariableFractionService $variableFractionService
     */
    public function __construct
    (
        Parser $parser, GeneratorService $generatorService, StringsHelper $stringsHelper, VariableFractionService $variableFractionService
    )
    {
        $this->parser = $parser;
        $this->generatorService = $generatorService;
        $this->stringsHelper = $stringsHelper;
        $this->variableFractionService = $variableFractionService;
    }

    /**
     * @param string $expression
     * @return float
     */
    public function evaluateExpression(string $expression): float
    {
        return $this->parser::solve($expression);
    }

    /**
     * @param EquationTemplateNP $data
     * @return LinearEquationTemplateNP
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiSyntaxException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function multiplyByLCM(EquationTemplateNP $data): EquationTemplateNP
    {
        if(!$variableFractionsWrapper = $this->variableFractionService->getVariableFractionData($data)){
            return $data;
        }

        bdump($variableFractionsWrapper);

        $data = $this->variableFractionService->getMultipliedByLCM($variableFractionsWrapper);

        return $data;

//
//        // TODO: MOVE INTO SEPARATED METHOD
//        $data->setVarFractions($this->variableFractionService->getVarFractions());
//        $data->setVarFractionsParDivider($this->variableFractionService->getVarFractionsParDivider());
//        $data->setFractionsToCheckCnt($data->calculateFractionsToCheckCnt());
//
//        if($data->getFractionsToCheckCnt() === 1){
//            $data->setFractionsToCheckIndexes([
//                $this->generatorService->generateInteger(0, $data->getVarFractionsParDividerCnt() - 1)
//            ]);
//        }
//
//        if($data->getFractionsToCheckCnt() === 2){
//            $toCheckIndexes = [];
//            $toCheckIndexes[] = $this->generatorService->generateInteger(0, $data->getVarFractionsParDividerCnt() - 1);
//            $toCheckIndexes[] = $this->generatorService->generateIntegerWithout(0, $data->getVarFractionsParDividerCnt() - 1, $toCheckIndexes);
//            $data->setFractionsToCheckIndexes($toCheckIndexes);
//        }
//        // TODO: MOVE INTO SEPARATED METHOD
//
//        bdump($this->variableFractionService);

//        return $data;
    }
}
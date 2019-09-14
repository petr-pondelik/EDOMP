<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.7.19
 * Time: 0:04
 */

namespace App\Plugins;

use App\Exceptions\ProblemTemplateException;
use App\Helpers\ConstHelper;
use App\Helpers\LatexHelper;
use App\Helpers\RegularExpressions;
use App\Helpers\StringsHelper;
use App\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
use App\Model\Persistent\Functionality\ProblemFinal\ArithmeticSequenceFinalFunctionality;
use App\Model\Persistent\Functionality\TemplateJsonDataFunctionality;
use App\Services\ConditionService;
use App\Services\GeneratorService;
use App\Services\MathService;
use App\Services\NewtonApiClient;
use App\Services\VariableFractionService;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;

/**
 * Class ArithmeticSequencePlugin
 * @package App\Plugins
 */
class ArithmeticSequencePlugin extends SequencePlugin
{
    /**
     * ArithmeticSequencePlugin constructor.
     * @param NewtonApiClient $newtonApiClient
     * @param MathService $mathService
     * @param ConditionService $conditionService
     * @param GeneratorService $generatorService
     * @param TemplateJsonDataFunctionality $templateJsonDataFunctionality
     * @param LatexHelper $latexHelper
     * @param StringsHelper $stringsHelper
     * @param VariableFractionService $variableDividers
     * @param ConstHelper $constHelper
     * @param RegularExpressions $regularExpressions
     * @param ArithmeticSequenceFinalFunctionality $arithmeticSequenceFinalFunctionality
     */
    public function __construct
    (
        NewtonApiClient $newtonApiClient, MathService $mathService, ConditionService $conditionService,
        GeneratorService $generatorService, TemplateJsonDataFunctionality $templateJsonDataFunctionality,
        LatexHelper $latexHelper, StringsHelper $stringsHelper, VariableFractionService $variableDividers,
        ConstHelper $constHelper, RegularExpressions $regularExpressions,
        ArithmeticSequenceFinalFunctionality $arithmeticSequenceFinalFunctionality
    )
    {
        parent::__construct($newtonApiClient, $mathService, $conditionService, $generatorService, $templateJsonDataFunctionality, $latexHelper, $stringsHelper, $variableDividers, $constHelper, $regularExpressions);
        $this->functionality = $arithmeticSequenceFinalFunctionality;
    }

    /**
     * @param ProblemTemplateNP $data
     * @return bool
     * @throws ProblemTemplateException
     * @throws \App\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     */
    public function validateType(ProblemTemplateNP $data): bool
    {
        if(!parent::validateType($data)){
            throw new ProblemTemplateException('Ze zadané šablony nelze vygenerovat aritmetickou posloupnost.');
        }

        bdump('VALIDATE ARITHMETIC SEQUENCE');

        // Get three first members of the sequence
        $a[] = $this->stringsHelper::fillMultipliers($this->stringsHelper::passValues($data->getStandardized(), [$data->getIndexVariable() => 1]), $data->getIndexVariable());
        $a[] = $this->stringsHelper::fillMultipliers($this->stringsHelper::passValues($data->getStandardized(), [$data->getIndexVariable() => 2]), $data->getIndexVariable());
        $a[] = $this->stringsHelper::fillMultipliers($this->stringsHelper::passValues($data->getStandardized(), [$data->getIndexVariable() => 3]), $data->getIndexVariable());

        $data->setFirstValues($a);

        // LET THE CONDITION SERVICE FIND MATCHING PARAMETERS --> IF THERE IS NONE, SEQUENCE ISN'T ARITHMETIC
        // REQUIRE STORE JSON DATA INTO TEMPLATE JSON DATA --> FLAG (IS_VALIDATION_DATA) --> DURING TEMPLATE CREATE, MERGE VALIDATION AND CONDITION JSON DATA
        try{
            $matches = $this->conditionService->findConditionsMatches([
                $this->constHelper::DIFFERENCE_VALIDATION => [
                    $this->constHelper::DIFFERENCE_EXISTS => [
                        'data' => $data
                    ]
                ]
            ]);
        } catch (\Exception $e){
            throw new ProblemTemplateException('Zadán nepodporovaný formát šablony.');
        }

        if(!$matches){
            throw new ProblemTemplateException('Ze zadané šablony nelze vygenerovat aritmetickou posloupnost.');
        }

        $matchesJson = Json::encode($matches);
        $this->templateJsonDataFunctionality->create(ArrayHash::from([
            'jsonData' => $matchesJson
        ]), $data->getIdHidden());

        return true;
    }

    /**
     * @param ProblemFinal $problem
     * @return ArrayHash
     * @throws \App\Exceptions\EquationException
     */
    public function evaluate(ProblemFinal $problem): ArrayHash
    {
        $data = parent::evaluate($problem);
        $difference = (string) round($data->res[$data->seqName . '_{' . '2}'] - $data->res[$data->seqName . '_{' . '1}'], 3);
        $data->res['Diference'] = $difference;
        $this->functionality->storeResult($problem->getId(), $data->res);
        return $data->res;
    }
}
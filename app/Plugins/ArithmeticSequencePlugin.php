<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.7.19
 * Time: 0:04
 */

namespace App\Plugins;

use App\Arguments\SequenceValidateArgument;
use App\Exceptions\ProblemTemplateFormatException;
use App\Model\Entity\ProblemFinal;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;

/**
 * Class ArithmeticSequencePlugin
 * @package App\Plugins
 */
class ArithmeticSequencePlugin extends SequencePlugin
{
    /**
     * @param SequenceValidateArgument $data
     * @return bool
     * @throws ProblemTemplateFormatException
     * @throws \App\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     */
    public function validateType(SequenceValidateArgument $data): bool
    {
        if(!parent::validateType($data)){
            return false;
        }

        bdump('VALIDATE ARITHMETIC SEQUENCE');

        $parametersInfo = $this->stringsHelper::extractParametersInfo($data->expression);

        $a1 = $this->stringsHelper::fillMultipliers($this->stringsHelper::passValues($data->standardized, [$data->variable => 1]), $data->variable);
        $a2 = $this->stringsHelper::fillMultipliers($this->stringsHelper::passValues($data->standardized, [$data->variable => 2]), $data->variable);
        $a3 = $this->stringsHelper::fillMultipliers($this->stringsHelper::passValues($data->standardized, [$data->variable => 3]), $data->variable);

        // LET THE CONDITION SERVICE FIND MATCHING PARAMETERS --> IF THERE IS NONE, SEQUENCE ISN'T ARITHMETIC
        // REQUIRE STORE JSON DATA INTO TEMPLATE JSON DATA --> FLAG (IS_VALIDATION_DATA) --> DURING TEMPLATE CREATE, MERGE VALIDATION AND CONDITION JSON DATA
        try{
            $matches = $this->conditionService->findConditionsMatches([
                $this->constHelper::DIFFERENCE_VALIDATION => [
                    $this->constHelper::DIFFERENCE_EXISTS => [
                        'parametersInfo' => $parametersInfo,
                        'data' => Json::encode([$a1, $a2, $a3])
                    ]
                ]
            ]);
        } catch (\Exception $e){
            throw new ProblemTemplateFormatException('Zadán chybný formát šablony.');
        }

        if(!$matches){
            return false;
        }

        $matchesJson = Json::encode($matches);
        $this->templateJsonDataFunctionality->create(ArrayHash::from([
            'jsonData' => $matchesJson
        ]), null, true);

        return true;
    }

    /**
     * @param ProblemFinal $problem
     * @return ArrayHash
     */
    public function evaluate(ProblemFinal $problem): ArrayHash
    {
        $data = parent::evaluate($problem);
        $difference = (string) round($data->res[$data->seqName . '_{' . '2}'] - $data->res[$data->seqName . '_{' . '1}'], 3);
        $data->res['Diference'] = $difference;
        return $data->res;
    }


}
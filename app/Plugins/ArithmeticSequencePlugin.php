<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.7.19
 * Time: 0:04
 */

namespace App\Plugins;

use App\Exceptions\ProblemTemplateException;
use App\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\Model\Persistent\Entity\ProblemFinal;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;

/**
 * Class ArithmeticSequencePlugin
 * @package App\Plugins
 */
class ArithmeticSequencePlugin extends SequencePlugin
{
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
        $a[] = $this->stringsHelper::fillMultipliers($this->stringsHelper::passValues($data->getStandardized(), [$data->getVariable() => 1]), $data->getVariable());
        $a[] = $this->stringsHelper::fillMultipliers($this->stringsHelper::passValues($data->getStandardized(), [$data->getVariable() => 2]), $data->getVariable());
        $a[] = $this->stringsHelper::fillMultipliers($this->stringsHelper::passValues($data->getStandardized(), [$data->getVariable() => 3]), $data->getVariable());

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
        return $data->res;
    }
}
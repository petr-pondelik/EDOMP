<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.7.19
 * Time: 19:21
 */

namespace App\Plugins;

use App\Arguments\SequenceValidateArgument;
use App\Exceptions\ProblemTemplateException;
use App\Model\Entity\ProblemFinal;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;

/**
 * Class GeometricSequencePlugin
 * @package App\Plugins
 */
class GeometricSequencePlugin extends SequencePlugin
{
    /**
     * @param SequenceValidateArgument $data
     * @return bool
     * @throws ProblemTemplateException
     * @throws \App\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     */
    public function validateType(SequenceValidateArgument $data): bool
    {
        if(!parent::validateType($data)){
            return false;
        }

        bdump('VALIDATE GEOMETRIC SEQUENCE');

        $parametersInfo = $this->stringsHelper::extractParametersInfo($data->expression);

        // $$ q_n = \frac{n - <par min="1" max="1"/>}{n} $$
        // What if q_1 = 0???

        $q1 = $this->stringsHelper::fillMultipliers($this->stringsHelper::passValues($data->standardized, [$data->variable => 1]), $data->variable);
        $q2 = $this->stringsHelper::fillMultipliers($this->stringsHelper::passValues($data->standardized, [$data->variable => 2]), $data->variable);
        $q3 = $this->stringsHelper::fillMultipliers($this->stringsHelper::passValues($data->standardized, [$data->variable => 3]), $data->variable);

        bdump([$q1, $q2, $q3]);

        try{
            $matches = $this->conditionService->findConditionsMatches([
                $this->constHelper::QUOTIENT_VALIDATION => [
                    $this->constHelper::QUOTIENT_EXISTS => [
                        'parametersInfo' => $parametersInfo,
                        'data' => Json::encode([$q1, $q2, $q3])
                    ]
                ]
            ]);
        } catch (\Exception $e){
            throw new ProblemTemplateException('Zadán chybný formát šablony.');
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
        $quotient = (string) round($data->res[$data->seqName . '_{' . '2}'] / $data->res[$data->seqName . '_{' . '1}'], 1);
        $data->res['Kvocient'] = $quotient;
        return $data->res;
    }
}
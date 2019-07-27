<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.7.19
 * Time: 0:04
 */

namespace App\Plugins;

use App\Arguments\SequenceValidateArgument;
use App\Model\Entity\ProblemFinal;
use Nette\Utils\ArrayHash;

/**
 * Class ArithmeticSequencePlugin
 * @package App\Plugins
 */
class ArithmeticSequencePlugin extends SequencePlugin
{
    /**
     * @param SequenceValidateArgument $data
     * @return bool
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validate(SequenceValidateArgument $data): bool
    {
        if(!parent::validate($data)){
            return false;
        }

        $params = [];
        $parametersInfo = $this->stringsHelper::extractParametersInfo($data->expression);
        for ($i = 0; $i < $parametersInfo->count; $i++) {
            $params['p' . $i] = ($i + 2);
        }

        $final = $this->stringsHelper::passValues($data->standardized, $params);

        bdump('VALIDATE ARITHMETIC SEQUENCE');
        $final = $this->newtonApiClient->simplify($final);

        bdump($final);

        $a1 = $this->stringsHelper::passValues($final, [$data->variable => 1]);
        $a2 = $this->stringsHelper::passValues($final, [$data->variable => 2]);
        $a3 = $this->stringsHelper::passValues($final, [$data->variable => 3]);

        $diff1 = $this->parser::solve('(' . $a2 . ')' . ' - ' . '(' . $a1 . ')');
        $diff2 = $this->parser::solve('(' . $a3 . ')' . ' - ' . '(' . $a2 . ')');

        return round($diff1, 2) === round($diff2, 2);
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
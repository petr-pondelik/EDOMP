<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.7.19
 * Time: 19:21
 */

namespace App\Plugins;

use App\Arguments\SequenceValidateArgument;
use App\Model\Entity\ProblemFinal;
use Nette\Utils\ArrayHash;

/**
 * Class GeometricSequencePlugin
 * @package App\Plugins
 */
class GeometricSequencePlugin extends SequencePlugin
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
        // $$ q_n = \frac{n - <par min="1" max="1"/>}{n} $$
        // What if q_1 = 0???
        for ($i = 0; $i < $parametersInfo->count; $i++) {
            $params['p' . $i] = ($i + 2);
        }

        bdump($data->standardized);

        $final = $this->stringsHelper::passValues($data->standardized, $params);

        bdump('VALIDATE GEOMETRIC SEQUENCE');

        $final = $this->newtonApiClient->simplify($final);
        bdump($final);

        bdump('TEST');

        $a1 = $this->stringsHelper::passValues($final, [$data->variable => 1]);
        $a2 = $this->stringsHelper::passValues($final, [$data->variable => 2]);
        $a3 = $this->stringsHelper::passValues($final, [$data->variable => 3]);

        bdump($this->parser::solve($a1));
        bdump($this->parser::solve($a2));

        $quot1 = $this->parser::solve('(' . $this->parser::solve($a2) . ')' . '/' . '(' . $this->parser::solve($a1) . ')');
        $quot2 = $this->parser::solve('(' . $this->parser::solve($a3) . ')' . '/' . '(' . $this->parser::solve($a2) . ')');

        return round($quot1, 2) === round($quot2, 2);
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
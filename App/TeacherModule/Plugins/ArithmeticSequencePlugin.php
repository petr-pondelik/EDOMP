<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.7.19
 * Time: 0:04
 */

namespace App\TeacherModule\Plugins;

use App\TeacherModule\Exceptions\ProblemTemplateException;
use App\TeacherModule\Model\NonPersistent\Entity\ArithmeticSequenceTemplateNP;
use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;

/**
 * Class ArithmeticSequencePlugin
 * @package App\TeacherModule\Plugins
 */
final class ArithmeticSequencePlugin extends SequencePlugin
{
    /**
     * @param ProblemTemplateNP $data
     * @return bool
     * @throws ProblemTemplateException
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function validateType(ProblemTemplateNP $data): bool
    {
        /**
         * @var ArithmeticSequenceTemplateNP $data
         */

        if(!parent::validateType($data)){
            throw new ProblemTemplateException('Ze zadané šablony nelze vygenerovat aritmetickou posloupnost.');
        }

        bdump('VALIDATE ARITHMETIC SEQUENCE');

        // Get three first members of the sequence
        $a[] = $this->stringsHelper::fillMultipliers($this->parameterParser->passValues($data->getStandardized(), [$data->getIndexVariable() => 1]), $data->getIndexVariable());
        $a[] = $this->stringsHelper::fillMultipliers($this->parameterParser->passValues($data->getStandardized(), [$data->getIndexVariable() => 2]), $data->getIndexVariable());
        $a[] = $this->stringsHelper::fillMultipliers($this->parameterParser->passValues($data->getStandardized(), [$data->getIndexVariable() => 3]), $data->getIndexVariable());

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
        $this->templateJsonDataFunctionality->create(ArrayHash::from([ 'jsonData' => $matchesJson ]), true, $data->getIdHidden());

        return true;
    }

    /**
     * @param ProblemFinal $problem
     * @return ArrayHash
     * @throws \App\TeacherModule\Exceptions\EquationException
     * @throws \Exception
     */
    public function evaluate(ProblemFinal $problem): ArrayHash
    {
        $data = parent::evaluate($problem);
        $difference = (string) round($data->res[$data->seqName . '_{' . '2}'] - $data->res[$data->seqName . '_{' . '1}'], 3);
        $data->res['Diference'] = $difference;
        $this->problemFinalFunctionality->storeResult($problem->getId(), $data->res);
        return $data->res;
    }
}
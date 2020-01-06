<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.7.19
 * Time: 19:21
 */

namespace App\TeacherModule\Plugins;

use App\TeacherModule\Exceptions\ProblemTemplateException;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal;
use App\TeacherModule\Model\NonPersistent\Entity\GeometricSequenceTemplateNP;
use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;

/**
 * Class GeometricSequencePlugin
 * @package App\TeacherModule\Plugins
 */
final class GeometricSequencePlugin extends SequencePlugin
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
         * @var GeometricSequenceTemplateNP $data
         */

        if (!parent::validateType($data)) {
            return false;
        }

        bdump('VALIDATE GEOMETRIC SEQUENCE');

        // Get three first members of the sequence
        $q[] = $this->stringsHelper::fillMultipliers($this->parameterParser->passValues($data->getStandardized(), [$data->getIndexVariable() => 1]), $data->getIndexVariable());
        $q[] = $this->stringsHelper::fillMultipliers($this->parameterParser->passValues($data->getStandardized(), [$data->getIndexVariable() => 2]), $data->getIndexVariable());
        $q[] = $this->stringsHelper::fillMultipliers($this->parameterParser->passValues($data->getStandardized(), [$data->getIndexVariable() => 3]), $data->getIndexVariable());

        $data->setFirstValues($q);

        try {
            $matches = $this->conditionService->findConditionsMatches([
                $this->constHelper::QUOTIENT_VALIDATION => [
                    $this->constHelper::QUOTIENT_EXISTS => [
                        'data' => $data
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            throw new ProblemTemplateException('Zadán chybný formát šablony.');
        }

        if (!$matches) {
            throw new ProblemTemplateException('Ze zadané šablony nelze vygenerovat geometrickou posloupnost.');
        }

        $matchesJson = Json::encode($matches);
        $this->templateJsonDataFunctionality->create(ArrayHash::from([
            'jsonData' => $matchesJson
        ]), true, $data->getId());

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
        $quotient = (string)round($data->res[$data->seqName . '_{' . '2}'] / $data->res[$data->seqName . '_{' . '1}'], 1);
        $data->res['Kvocient'] = $quotient;
        $this->problemFinalFunctionality->storeResult($problem->getId(), $data->res);
        return $data->res;
    }
}
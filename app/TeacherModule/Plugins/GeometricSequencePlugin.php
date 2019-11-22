<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.7.19
 * Time: 19:21
 */

namespace App\TeacherModule\Plugins;

use App\TeacherModule\Exceptions\ProblemTemplateException;
use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Helpers\LatexHelper;
use App\CoreModule\Helpers\RegularExpressions;
use App\CoreModule\Helpers\StringsHelper;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
use App\CoreModule\Model\Persistent\Functionality\ProblemFinal\GeometricSequenceFinalFunctionality;
use App\CoreModule\Model\Persistent\Functionality\TemplateJsonDataFunctionality;
use App\TeacherModule\Services\ConditionService;
use App\TeacherModule\Services\MathService;
use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\TeacherModule\Services\NewtonApiClient;
use App\TeacherModule\Services\ProblemGenerator;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;

/**
 * Class GeometricSequencePlugin
 * @package App\TeacherModule\Plugins
 */
final class GeometricSequencePlugin extends SequencePlugin
{
    /**
     * GeometricSequencePlugin constructor.
     * @param NewtonApiClient $newtonApiClient
     * @param MathService $mathService
     * @param ConditionService $conditionService
     * @param ProblemGenerator $problemGenerator
     * @param TemplateJsonDataFunctionality $templateJsonDataFunctionality
     * @param LatexHelper $latexHelper
     * @param StringsHelper $stringsHelper
     * @param ConstHelper $constHelper
     * @param RegularExpressions $regularExpressions
     * @param GeometricSequenceFinalFunctionality $geometricSequenceFinalFunctionality
     */
    public function __construct
    (
        NewtonApiClient $newtonApiClient, MathService $mathService, ConditionService $conditionService,
        ProblemGenerator $problemGenerator, TemplateJsonDataFunctionality $templateJsonDataFunctionality,
        LatexHelper $latexHelper, StringsHelper $stringsHelper,
        ConstHelper $constHelper, RegularExpressions $regularExpressions,
        GeometricSequenceFinalFunctionality $geometricSequenceFinalFunctionality
    )
    {
        parent::__construct($newtonApiClient, $mathService, $conditionService, $problemGenerator, $templateJsonDataFunctionality, $latexHelper, $stringsHelper, $constHelper, $regularExpressions);
        $this->functionality = $geometricSequenceFinalFunctionality;
    }

    /**
     * @param ProblemTemplateNP $data
     * @return bool
     * @throws ProblemTemplateException
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     */
    public function validateType(ProblemTemplateNP $data): bool
    {
        if(!parent::validateType($data)){
            return false;
        }

        bdump('VALIDATE GEOMETRIC SEQUENCE');

        // Get three first members of the sequence
        $q[] = $this->stringsHelper::fillMultipliers($this->stringsHelper::passValues($data->getStandardized(), [$data->getIndexVariable() => 1]), $data->getIndexVariable());
        $q[] = $this->stringsHelper::fillMultipliers($this->stringsHelper::passValues($data->getStandardized(), [$data->getIndexVariable() => 2]), $data->getIndexVariable());
        $q[] = $this->stringsHelper::fillMultipliers($this->stringsHelper::passValues($data->getStandardized(), [$data->getIndexVariable() => 3]), $data->getIndexVariable());

        $data->setFirstValues($q);

        try{
            $matches = $this->conditionService->findConditionsMatches([
                $this->constHelper::QUOTIENT_VALIDATION => [
                    $this->constHelper::QUOTIENT_EXISTS => [
                        'data' => $data
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
        ]), true, $data->getIdHidden());

        return true;
    }

    /**
     * @param ProblemFinal $problem
     * @return ArrayHash
     * @throws \App\TeacherModule\Exceptions\EquationException
     */
    public function evaluate(ProblemFinal $problem): ArrayHash
    {
        $data = parent::evaluate($problem);
        $quotient = (string) round($data->res[$data->seqName . '_{' . '2}'] / $data->res[$data->seqName . '_{' . '1}'], 1);
        $data->res['Kvocient'] = $quotient;
        $this->functionality->storeResult($problem->getId(), $data->res);
        return $data->res;
    }
}
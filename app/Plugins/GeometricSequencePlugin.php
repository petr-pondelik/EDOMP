<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.7.19
 * Time: 19:21
 */

namespace App\Plugins;

use App\Exceptions\ProblemTemplateException;
use App\Helpers\ConstHelper;
use App\Helpers\LatexHelper;
use App\Helpers\RegularExpressions;
use App\Helpers\StringsHelper;
use App\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
use App\Model\Persistent\Functionality\ProblemFinal\GeometricSequenceFinalFunctionality;
use App\Model\Persistent\Functionality\TemplateJsonDataFunctionality;
use App\Services\ConditionService;
use App\Services\GeneratorService;
use App\Services\MathService;
use App\Services\NewtonApiClient;
use App\Services\VariableFractionService;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;

/**
 * Class GeometricSequencePlugin
 * @package App\Plugins
 */
class GeometricSequencePlugin extends SequencePlugin
{
    /**
     * GeometricSequencePlugin constructor.
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
     * @param GeometricSequenceFinalFunctionality $geometricSequenceFinalFunctionality
     */
    public function __construct
    (
        NewtonApiClient $newtonApiClient, MathService $mathService, ConditionService $conditionService,
        GeneratorService $generatorService, TemplateJsonDataFunctionality $templateJsonDataFunctionality,
        LatexHelper $latexHelper, StringsHelper $stringsHelper, VariableFractionService $variableDividers,
        ConstHelper $constHelper, RegularExpressions $regularExpressions,
        GeometricSequenceFinalFunctionality $geometricSequenceFinalFunctionality
    )
    {
        parent::__construct($newtonApiClient, $mathService, $conditionService, $generatorService, $templateJsonDataFunctionality, $latexHelper, $stringsHelper, $variableDividers, $constHelper, $regularExpressions);
        $this->functionality = $geometricSequenceFinalFunctionality;
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
        $quotient = (string) round($data->res[$data->seqName . '_{' . '2}'] / $data->res[$data->seqName . '_{' . '1}'], 1);
        $data->res['Kvocient'] = $quotient;
        $this->functionality->storeResult($problem->getId(), $data->res);
        return $data->res;
    }
}
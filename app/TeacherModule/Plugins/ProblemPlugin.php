<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.7.19
 * Time: 21:06
 */

namespace App\TeacherModule\Plugins;

use App\Exceptions\InvalidParameterException;
use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Helpers\LatexHelper;
use App\CoreModule\Helpers\RegularExpressions;
use App\CoreModule\Helpers\StringsHelper;
use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use App\Model\Persistent\Functionality\BaseFunctionality;
use App\TeacherModule\Services\VariableFractionService;
use App\Model\Persistent\Functionality\TemplateJsonDataFunctionality;
use App\TeacherModule\Services\ConditionService;
use App\TeacherModule\Services\MathService;
use App\TeacherModule\Services\NewtonApiClient;
use App\TeacherModule\Services\ProblemGenerator;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class ProblemPlugin
 * @package App\TeacherModule\Plugins
 */
abstract class ProblemPlugin implements IProblemPlugin
{
    /**
     * @var NewtonApiClient
     */
    protected $newtonApiClient;

    /**
     * @var MathService
     */
    protected $mathService;

    /**
     * @var ConditionService
     */
    protected $conditionService;

    /**
     * @var ProblemGenerator
     */
    protected $generatorService;

    /**
     * @var BaseFunctionality
     */
    protected $functionality;

    /**
     * @var TemplateJsonDataFunctionality
     */
    protected $templateJsonDataFunctionality;

    /**
     * @var LatexHelper
     */
    protected $latexHelper;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * @var RegularExpressions
     */
    protected $regularExpressions;

    /**
     * @var VariableFractionService
     */
    protected $variableDividers;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * ProblemPlugin constructor.
     * @param NewtonApiClient $newtonApiClient
     * @param MathService $mathService
     * @param ConditionService $conditionService
     * @param ProblemGenerator $generatorService
     * @param TemplateJsonDataFunctionality $templateJsonDataFunctionality
     * @param LatexHelper $latexHelper
     * @param StringsHelper $stringsHelper
     * @param VariableFractionService $variableDividers
     * @param ConstHelper $constHelper
     * @param RegularExpressions $regularExpressions
     */
    public function __construct
    (
        NewtonApiClient $newtonApiClient,
        MathService $mathService,
        ConditionService $conditionService,
        ProblemGenerator $generatorService,
        TemplateJsonDataFunctionality $templateJsonDataFunctionality,
        LatexHelper $latexHelper, StringsHelper $stringsHelper,
        VariableFractionService $variableDividers,
        ConstHelper $constHelper,
        RegularExpressions $regularExpressions
    )
    {
        $this->newtonApiClient = $newtonApiClient;
        $this->mathService = $mathService;
        $this->conditionService = $conditionService;
        $this->generatorService = $generatorService;
        $this->templateJsonDataFunctionality = $templateJsonDataFunctionality;
        $this->latexHelper = $latexHelper;
        $this->stringsHelper = $stringsHelper;
        $this->variableDividers = $variableDividers;
        $this->constHelper = $constHelper;
        $this->regularExpressions = $regularExpressions;
    }

    /**
     * @param string $expression
     * @throws InvalidParameterException
     */
    protected function validateParameters(string $expression): void
    {
        bdump('VALIDATE PARAMETERS');

        if(!Strings::match($expression,'~' . $this->regularExpressions::RE_PARAMETER_OPENING . '~')) {
            throw new InvalidParameterException('Zadaná šablona neobsahuje parametr.');
        }

        $split = $this->stringsHelper::findParametersAll($expression);

        bdump($split);

        foreach ($split as $part) {
            if ($part !== '' && Strings::startsWith($part, '<par')) {

                if (!Strings::match($part, '~' . $this->regularExpressions::RE_PARAMETER_VALID . '~')) {
                    throw new InvalidParameterException('Zadaná šablona obsahuje nevalidní parametr.');
                }

                $min = $this->stringsHelper::extractParAttr($part, 'min');
                $max = $this->stringsHelper::extractParAttr($part, 'max');

                if ($min > $max) {
                    throw new InvalidParameterException('Neplatný interval parametru.');
                }
            }
        }

    }

    /**
     * @param ProblemTemplate $problemTemplate
     * @param array|null $usedMatchesInx
     * @return ArrayHash
     * @throws \App\Exceptions\GeneratorException
     * @throws \Nette\Utils\JsonException
     */
    public function constructProblemFinalData(ProblemTemplate $problemTemplate, ?array $usedMatchesInx): ArrayHash
    {
        [$finalBody, $matchesIndex] = $this->generatorService->generateProblemFinalBody($problemTemplate, $usedMatchesInx);
        bdump($finalBody);
        $finalData = ArrayHash::from([
            'textBefore' => $problemTemplate->getTextBefore(),
            'body' => $finalBody,
            'textAfter' => $problemTemplate->getTextAfter(),
            'difficulty' => $problemTemplate->getDifficulty()->getId(),
            'problemType' => $problemTemplate->getProblemType()->getId(),
            'subCategory' => $problemTemplate->getSubCategory()->getId(),
            'problemTemplateId' => $problemTemplate->getId(),
            'matchesIndex' => $matchesIndex,
            'isGenerated' => true
        ]);
        return $finalData;
    }

    /**
     * @param ProblemTemplate $problemTemplate
     * @param array|null $usedMatchesInx
     * @return ProblemFinal
     * @throws \App\Exceptions\GeneratorException
     * @throws \Nette\Utils\JsonException
     */
    public function constructProblemFinal(ProblemTemplate $problemTemplate, ?array $usedMatchesInx): ProblemFinal
    {
        $finalData = $this->constructProblemFinalData($problemTemplate, $usedMatchesInx);
        $conditions = $problemTemplate->getConditions()->getValues();
        $problemFinal = $this->functionality->create($finalData, false, $conditions);
        $problemFinal->setBody($this->latexHelper->postprocessProblemFinalBody($problemFinal->getBody()));
        return $problemFinal;
    }

    /**
     * @param ProblemTemplateNP $data
     * @return bool
     */
    abstract public function validateType(ProblemTemplateNP $data): bool;
}
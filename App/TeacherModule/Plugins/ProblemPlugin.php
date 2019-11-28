<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.7.19
 * Time: 21:06
 */

namespace App\TeacherModule\Plugins;

use App\CoreModule\Helpers\StringsHelper;
use App\CoreModule\Model\Persistent\Functionality\ProblemFinalFunctionality;
use App\TeacherModule\Exceptions\InvalidParameterException;
use App\CoreModule\Helpers\ConstHelper;
use App\TeacherModule\Services\LatexParser;
use App\CoreModule\Helpers\RegularExpressions;
use App\TeacherModule\Interfaces\IProblemPlugin;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal;
use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use App\CoreModule\Model\Persistent\Functionality\BaseFunctionality;
use App\CoreModule\Model\Persistent\Functionality\TemplateJsonDataFunctionality;
use App\TeacherModule\Services\ConditionService;
use App\TeacherModule\Services\MathService;
use App\TeacherModule\Services\NewtonApiClient;
use App\TeacherModule\Services\ParameterParser;
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
    protected $problemGenerator;

    /**
     * @var ProblemFinalFunctionality
     */
    protected $problemFinalFunctionality;

    /**
     * @var TemplateJsonDataFunctionality
     */
    protected $templateJsonDataFunctionality;

    /**
     * @var LatexParser
     */
    protected $latexParser;

    /**
     * @var ParameterParser
     */
    protected $parameterParser;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * @var RegularExpressions
     */
    protected $regularExpressions;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * ProblemPlugin constructor.
     * @param NewtonApiClient $newtonApiClient
     * @param MathService $mathService
     * @param ConditionService $conditionService
     * @param ProblemGenerator $problemGenerator
     * @param ProblemFinalFunctionality $problemFinalFunctionality
     * @param TemplateJsonDataFunctionality $templateJsonDataFunctionality
     * @param LatexParser $latexParser
     * @param ParameterParser $parameterParser
     * @param StringsHelper $stringsHelper
     * @param ConstHelper $constHelper
     * @param RegularExpressions $regularExpressions
     */
    public function __construct
    (
        NewtonApiClient $newtonApiClient,
        MathService $mathService,
        ConditionService $conditionService,
        ProblemGenerator $problemGenerator,
        ProblemFinalFunctionality $problemFinalFunctionality,
        TemplateJsonDataFunctionality $templateJsonDataFunctionality,
        LatexParser $latexParser,
        ParameterParser $parameterParser,
        StringsHelper $stringsHelper,
        ConstHelper $constHelper,
        RegularExpressions $regularExpressions
    )
    {
        $this->newtonApiClient = $newtonApiClient;
        $this->mathService = $mathService;
        $this->conditionService = $conditionService;
        $this->problemGenerator = $problemGenerator;
        $this->problemFinalFunctionality = $problemFinalFunctionality;
        $this->templateJsonDataFunctionality = $templateJsonDataFunctionality;
        $this->latexParser = $latexParser;
        $this->parameterParser = $parameterParser;
        $this->stringsHelper = $stringsHelper;
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

        $split = $this->parameterParser::findParametersAll($expression);

        foreach ($split as $part) {
            if ($part !== '' && Strings::startsWith($part, '<par')) {

                if (!Strings::match($part, '~' . $this->regularExpressions::RE_PARAMETER_VALID . '~')) {
                    throw new InvalidParameterException('Zadaná šablona obsahuje nevalidní parametr.');
                }

                $min = $this->parameterParser::extractParAttr($part, 'min');
                $max = $this->parameterParser::extractParAttr($part, 'max');

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
     * @throws \App\TeacherModule\Exceptions\GeneratorException
     * @throws \Nette\Utils\JsonException
     */
    protected function constructFinalData(ProblemTemplate $problemTemplate, ?array $usedMatchesInx): ArrayHash
    {
        bdump('CONSTRUCT PROBLEM FINAL DATA');
        [$finalBody, $matchesIndex] = $this->problemGenerator->generate($problemTemplate, $usedMatchesInx);
        $finalData = ArrayHash::from([
            'textBefore' => $problemTemplate->getTextBefore(),
            'body' => $finalBody,
            'textAfter' => $problemTemplate->getTextAfter(),
            'difficulty' => $problemTemplate->getDifficulty()->getId(),
            'problemType' => $problemTemplate->getProblemType()->getId(),
            'subTheme' => $problemTemplate->getSubTheme()->getId(),
            'problemTemplateId' => $problemTemplate->getId(),
            'matchesIndex' => $matchesIndex,
            'isGenerated' => true,
            'studentVisible' => $problemTemplate->isStudentVisible(),
            'userId' => $problemTemplate->getCreatedBy()->getId()
        ]);
        return $finalData;
    }

    /**
     * @param ProblemFinal $entity
     * @return ProblemFinal
     */
    protected function postprocessFinal(ProblemFinal $entity): ProblemFinal
    {
        $entity->setBody($this->latexParser->postprocessFinalBody($entity->getBody()));
        return $entity;
    }

    /**
     * @param ProblemTemplate $problemTemplate
     * @param array|null $usedMatchesInx
     * @return ProblemFinal
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \App\TeacherModule\Exceptions\GeneratorException
     * @throws \Nette\Utils\JsonException
     */
    public function createFinal(ProblemTemplate $problemTemplate, ?array $usedMatchesInx): ProblemFinal
    {
        $finalData = $this->constructFinalData($problemTemplate, $usedMatchesInx);
//        $conditions = $problemTemplate->getConditions()->getValues();
        $problemFinal = $this->problemFinalFunctionality->create($finalData, false);
        return $this->postprocessFinal($problemFinal);
    }
}
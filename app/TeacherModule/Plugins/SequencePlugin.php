<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.7.19
 * Time: 0:00
 */

namespace App\TeacherModule\Plugins;

use App\TeacherModule\Exceptions\NewtonApiSyntaxException;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class SequencePlugin
 * @package App\TeacherModule\Plugins
 */
abstract class SequencePlugin extends ProblemPlugin
{
    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return ProblemTemplateNP
     * @throws \App\TeacherModule\Exceptions\EquationException
     * @throws \App\TeacherModule\Exceptions\NewtonApiException
     * @throws \App\TeacherModule\Exceptions\NewtonApiRequestException
     * @throws \App\TeacherModule\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function preprocess(ProblemTemplateNP $problemTemplate): ProblemTemplateNP
    {
        bdump('PREPROCESS SEQUENCE');
        $expression = $this->latexParser::parse($problemTemplate->getBody());
        $problemTemplate->setExpression($expression);
        $parametrized = $this->parameterParser::parse($expression);
        $sides = $this->stringsHelper::getEquationSides($parametrized->expression);
        $expression = $this->newtonApiClient->simplify($sides->right);
        $problemTemplate->setStandardized($expression);
        return $problemTemplate;
    }

    /**
     * @param string $expression
     * @return string
     * @throws \App\TeacherModule\Exceptions\EquationException
     * @throws \App\TeacherModule\Exceptions\NewtonApiException
     * @throws \App\TeacherModule\Exceptions\NewtonApiRequestException
     * @throws \App\TeacherModule\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function standardizeFinal(string $expression): string
    {
        bdump('STANDARDIZE SEQUENCE');
        $expression = $this->latexParser::parse($expression);
        $parametrized = $this->parameterParser::parse($expression);
        $sides = $this->stringsHelper::getEquationSides($parametrized->expression);
        $expression = $this->newtonApiClient->simplify($sides->right);
        return $expression;
    }

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return bool
     */
    public function validateType(ProblemTemplateNP $problemTemplate): bool
    {
        bdump('VALIDATE SEQUENCE');
        if(!Strings::match($problemTemplate->getExpression(), '~' . $this->regularExpressions::getSequenceRE($problemTemplate->getIndexVariable()) . '~')){
            return false;
        }
        return true;
    }

    /**
     * @param ProblemFinal $problem
     * @return ArrayHash
     * @throws \App\TeacherModule\Exceptions\EquationException
     */
    public function evaluate(ProblemFinal $problem): ArrayHash
    {
        $parsed = $this->latexParser::parse($problem->getBody());
        $variable = $problem->getVariable();

        $sides = $this->stringsHelper::getEquationSides($parsed, false);
        $seqName = $this->stringsHelper::extractSequenceName($sides->left);

        //$problem = $this->problemFinalRepository->find($problem->getId());
        $firstN = $problem->getFirstN();
        $res = [];

        $sides->right = $this->stringsHelper::fillMultipliers($sides->right, $variable);

        for($i = 1; $i <= $firstN; $i++){
            $res[$seqName . '_{' . $i . '}'] = $this->mathService->evaluateExpression(
                $this->parameterParser->passValues($sides->right, [
                    $variable => $i
                ])
            );
        }

        return ArrayHash::from([
            'seqName' => $seqName,
            'res' => $res
        ]);
    }

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return int
     * @throws \App\TeacherModule\Exceptions\InvalidParameterException
     * @throws \App\TeacherModule\Exceptions\NewtonApiException
     * @throws \App\TeacherModule\Exceptions\NewtonApiRequestException
     * @throws \App\TeacherModule\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validateBody(ProblemTemplateNP $problemTemplate): int
    {
        if(!$this->latexParser::latexWrapped($problemTemplate->getBody())){
            return 1;
        }
        $parsed = $this->latexParser::parse($problemTemplate->getBody());

        $this->validateParameters($problemTemplate->getBody());
        $split = $this->parameterParser::splitByParameters($parsed);

        if (empty($problemTemplate->getIndexVariable()) || !$this->stringsHelper::containsVariable($split, $problemTemplate->getIndexVariable())) {
            return 2;
        }

        $parametrized = $this->parameterParser::parse($parsed);

        try {
            $this->newtonApiClient->simplify($parametrized->expression);
        } catch (NewtonApiSyntaxException $e) {
            return 3;
        }

        return -1;
    }

    /**
     * @param ProblemTemplate $problemTemplate
     * @param array|null $usedMatchesInx
     * @return ArrayHash
     * @throws \App\TeacherModule\Exceptions\GeneratorException
     * @throws \Nette\Utils\JsonException
     */
    public function constructFinalData(ProblemTemplate $problemTemplate, ?array $usedMatchesInx): ArrayHash
    {
        $finalData = parent::constructFinalData($problemTemplate, $usedMatchesInx);
        $finalData->indexVariable = $problemTemplate->getIndexVariable();
        $finalData->firstN = $problemTemplate->getFirstN();
        return $finalData;
    }
}
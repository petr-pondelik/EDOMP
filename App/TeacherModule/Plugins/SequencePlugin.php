<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.7.19
 * Time: 0:00
 */

namespace App\TeacherModule\Plugins;

use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ArithmeticSequenceTemplate;
use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\GeometricSequenceTemplate;
use App\TeacherModule\Exceptions\NewtonApiSyntaxException;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal;
use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\TeacherModule\Model\NonPersistent\Entity\SequenceTemplateNP;
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
        $sides = $this->mathService::getEquationSides($parametrized->expression);
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
        $sides = $this->mathService::getEquationSides($parametrized->expression);
        $expression = $this->newtonApiClient->simplify($sides->right);
        return $expression;
    }

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return bool
     */
    public function validateType(ProblemTemplateNP $problemTemplate): bool
    {
        bdump('VALIDATE SEQUENCE TYPE');
        /**
         * @var SequenceTemplateNP $problemTemplate
         */
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
        /**
         * @var ArithmeticSequenceTemplate|GeometricSequenceTemplate $template
         */
        $template = $problem->getProblemTemplate();
        $parsed = $this->latexParser::parse($problem->getBody());
        $variable = $template->getIndexVariable();

        $sides = $this->mathService::getEquationSides($parsed, false);
        $seqName = self::extractSequenceName($sides->left);

        //$problem = $this->problemFinalRepository->find($problem->getId());
        $firstN = $template->getFirstN();
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
        /**
         * @var SequenceTemplateNP $problemTemplate
         */
        if(!$this->latexParser::latexWrapped($problemTemplate->getBody())){
            return 1;
        }
        $parsed = $this->latexParser::parse($problemTemplate->getBody());

        $this->validateParameters($problemTemplate->getBody());
        $split = $this->parameterParser::splitByParameters($parsed);

        if (empty($problemTemplate->getIndexVariable()) || !$this->mathService::containsVariable($split, $problemTemplate->getIndexVariable())) {
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
     * @param string $expression
     * @return string
     */
    public static function extractSequenceName(string $expression): string
    {
        return (Strings::match($expression, '~^\s*(\w*)\w$~'))[1];
    }
}
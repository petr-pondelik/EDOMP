<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.7.19
 * Time: 22:21
 */

namespace App\TeacherModule\Plugins;

use App\TeacherModule\Exceptions\ProblemTemplateException;
use App\CoreModule\Helpers\ConstHelper;
use App\TeacherModule\Services\LatexParser;
use App\CoreModule\Helpers\RegularExpressions;
use App\CoreModule\Helpers\StringsHelper;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
use App\CoreModule\Model\Persistent\Functionality\ProblemFinal\QuadraticEquationFinalFunctionality;
use App\CoreModule\Model\Persistent\Functionality\TemplateJsonDataFunctionality;
use App\TeacherModule\Services\ConditionService;
use App\TeacherModule\Services\MathService;
use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\TeacherModule\Model\NonPersistent\Entity\QuadraticEquationTemplateNP;
use App\TeacherModule\Services\NewtonApiClient;
use App\TeacherModule\Services\ProblemGenerator;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;
use Nette\Utils\Strings;

/**
 * Class QuadraticEquationPlugin
 * @package App\TeacherModule\Plugins
 */
final class QuadraticEquationPlugin extends EquationPlugin
{
    /**
     * QuadraticEquationPlugin constructor.
     * @param NewtonApiClient $newtonApiClient
     * @param MathService $mathService
     * @param ConditionService $conditionService
     * @param ProblemGenerator $problemGenerator
     * @param TemplateJsonDataFunctionality $templateJsonDataFunctionality
     * @param LatexParser $latexParser
     * @param StringsHelper $stringsHelper
     * @param ConstHelper $constHelper
     * @param RegularExpressions $regularExpressions
     * @param QuadraticEquationFinalFunctionality $quadraticEquationFinalFunctionality
     */
    public function __construct
    (
        NewtonApiClient $newtonApiClient, MathService $mathService, ConditionService $conditionService,
        ProblemGenerator $problemGenerator, TemplateJsonDataFunctionality $templateJsonDataFunctionality,
        LatexParser $latexParser, StringsHelper $stringsHelper,
        ConstHelper $constHelper, RegularExpressions $regularExpressions,
        QuadraticEquationFinalFunctionality $quadraticEquationFinalFunctionality
    )
    {
        parent::__construct($newtonApiClient, $mathService, $conditionService, $problemGenerator, $templateJsonDataFunctionality, $latexParser, $stringsHelper, $constHelper, $regularExpressions);
        $this->functionality = $quadraticEquationFinalFunctionality;
    }

    /**
     * @param string $expression
     * @param string $variable
     * @return false|string
     * @throws \App\TeacherModule\Exceptions\NewtonApiException
     * @throws \App\TeacherModule\Exceptions\NewtonApiRequestException
     * @throws \App\TeacherModule\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDiscriminantA(string $expression, string $variable)
    {
        bdump('GET DISCRIMINANT A');
        $aExp = Strings::match($expression, '~' . sprintf($this->regularExpressions::RE_DISCRIMINANT_A_COEFFICIENT, $variable) . '~');
        $prefix = Strings::trim($aExp[1]);
        $postfix = Strings::trim($aExp[2]);

        if($prefix === '' || $prefix === '+'){
            $prefix = '1';
        }

        if($prefix === '-'){
            $prefix = '-1';
        }

        if($postfix !== ''){
            $res = '(' . $postfix . ')^(-1) ' . '(' . $prefix . ')';
        }
        else{
            $res = $prefix;
        }

        if($res !== '1' && $res !== '-1'){
            $res = $this->newtonApiClient->simplify($res);
        }

        return $this->stringsHelper::wrap($res);
    }

    /**
     * @param string $standardized
     * @param string $variable
     * @return false|string
     * @throws \App\TeacherModule\Exceptions\NewtonApiException
     * @throws \App\TeacherModule\Exceptions\NewtonApiRequestException
     * @throws \App\TeacherModule\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDiscriminantB(string $standardized, string $variable)
    {
        bdump('GET DISCRIMINANT B');

        $bExp = Strings::after($standardized, $variable . '^2');
        $bExp = Strings::replace($bExp, '~' . $this->regularExpressions::RE_FIRST_OPERATOR_SPLIT . '~', '$2$3');

        $matchArr = Strings::match($bExp, '~' . sprintf($this->regularExpressions::RE_DISCRIMINANT_B_COEFFICIENT, $variable) . '~');

        $prefix = Strings::trim($matchArr[1]);
        $postfix = Strings::trim($matchArr[2]);

        if($prefix === '+' || $prefix === ''){
            $prefix = '1';
        }

        if($prefix === '-'){
            $prefix = '-1';
        }

        if($postfix !== ''){
            $res = '(' . $postfix . ')^(-1) ' . '(' . $prefix . ')';
        }
        else{
            $res = $prefix;
        }

        if($res !== '1' && $res !== '-1'){
            $res = $this->newtonApiClient->simplify($res);
        }

        return $this->stringsHelper::wrap($res);
    }

    /**
     * @param string $expression
     * @param string $variable
     * @return false|string
     * @throws \App\TeacherModule\Exceptions\NewtonApiException
     * @throws \App\TeacherModule\Exceptions\NewtonApiRequestException
     * @throws \App\TeacherModule\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDiscriminantC(string $expression, string $variable)
    {
        bdump('GET DISCRIMINANT C');

        $cExp = Strings::after($expression, ' ' . $variable . ' ');
        $matchArr = Strings::match($cExp, '~' . $this->regularExpressions::RE_DISCRIMINANT_C_COEFFICIENT . '~');
        $res = $matchArr[2];

        if(!$res){
            $res = Strings::after($expression, $variable . '^2');
            $matchArr = Strings::match($res, '~' . $this->regularExpressions::RE_DISCRIMINANT_C_COEFFICIENT . '~');
            $res = Strings::trim($matchArr[2]);
            if($res === '' || Strings::contains($res, $variable)){
                bdump('GET DISCRIMINANT C RES');
                bdump('0');
                return '0';
            }
        }

        $res = $this->newtonApiClient->simplify($res);
        return $this->stringsHelper::wrap($res);
    }

    /**
     * @param string $standardized
     * @param string $variable
     * @return string
     * @throws \App\TeacherModule\Exceptions\NewtonApiException
     * @throws \App\TeacherModule\Exceptions\NewtonApiRequestException
     * @throws \App\TeacherModule\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDiscriminantExpression(string $standardized, string $variable): string
    {
        return $this->stringsHelper::fillMultipliers($this->getDiscriminantB($standardized, $variable) . '^2' . ' - 4 * ' . $this->getDiscriminantA($standardized, $variable) . ' * ' . $this->getDiscriminantC($standardized, $variable));
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
        bdump('VALIDATE QUADRATIC EQUATION');

        // Remove all the spaces
        $standardized = $this->stringsHelper::removeWhiteSpaces($data->getStandardized());

        // Match string against the quadratic expression regexp
        $matches = Strings::match($standardized, '~' . $this->regularExpressions::getQuadraticEquationRE($data->getVariable()) . '~');

        // Check if the whole expression was matched
        if($matches[0] !== $standardized){
            throw new ProblemTemplateException('Ze zadané šablony nelze vygenerovat kvadratickou rovnici.');
        }

        bdump('VARIABLE COEFFICIENTS');
        $variableCoefficients = Strings::matchAll($standardized, '~' . sprintf($this->regularExpressions::RE_VARIABLE_COEFFICIENT, $data->getVariable()) . '~');

        foreach ($variableCoefficients as $variableCoefficient){
            if($variableCoefficient[2] > 2){
                if($variableCoefficient[1] === '' || Strings::match($variableCoefficient[1], '~' . $this->regularExpressions::RE_NUM_FRAC . '~')){
                    throw new ProblemTemplateException('Ze zadané šablony nelze vygenerovat kvadratickou rovnici.');
                }
            }
        }

        try{
            $matches = $this->conditionService->findConditionsMatches([
                $this->constHelper::QUADRATIC_EQUATION_VALIDATION => [
                    $this->constHelper::IS_QUADRATIC_EQUATION => [
                        'data' => $data
                    ]
                ]
            ]);
        } catch (\Exception $e){
            bdump($e);
            throw new ProblemTemplateException('Zadán nepodporovaný formát šablony.');
        }

        if(!$matches){
            throw new ProblemTemplateException('Ze zadané šablony nelze vygenerovat kvadratickou rovnici.');
        }

        $matchesJson = Json::encode($matches);
        $this->templateJsonDataFunctionality->create(ArrayHash::from(['jsonData' => $matchesJson]), true, $data->getIdHidden());

        return true;
    }

    /**
     * @param ProblemFinal $problem
     * @return ArrayHash
     * @throws \App\TeacherModule\Exceptions\EquationException
     * @throws \App\TeacherModule\Exceptions\NewtonApiException
     * @throws \App\TeacherModule\Exceptions\NewtonApiRequestException
     * @throws \App\TeacherModule\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function evaluate(ProblemFinal $problem): ArrayHash
    {
        $standardized = $this->standardizeFinal($problem->getBody());
        $a = $this->getDiscriminantA($standardized, $problem->getVariable());
        $b = $this->getDiscriminantB($standardized, $problem->getVariable());
        $discriminant = $this->getDiscriminantExpression($standardized, $problem->getVariable());
        $discriminant = $this->mathService->evaluateExpression($discriminant);

        $b = $this->mathService->evaluateExpression($b);
        $a = $this->mathService->evaluateExpression($a);

        if($discriminant > 0){
            $res1 = ((-$b) + sqrt($discriminant)) / (2*$a);
            $res2 = ((-$b) - sqrt($discriminant)) / (2*$a);
            //bdump(sqrt($discriminant));
            $res = [
                'type' => 'double',
                $problem->getVariable() . '_1' => $res1,
                $problem->getVariable() . '_2' => $res2
            ];
        }
        else if((int) $discriminant === 0){
            $res1 = ((-$b) + sqrt($discriminant)) / (2*$a);
            $res = [
                'type' => 'single',
                $problem->getVariable() => $res1
            ];
        }
        else{
            $res = [
                'type' => 'complex',
                $problem->getVariable() => 'complex'
            ];
        }

        $res = ArrayHash::from($res);

        $this->functionality->storeResult($problem->getId(), $res);

        return $res;
    }

    /**
     * @param QuadraticEquationTemplateNP $data
     * @return bool
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \App\TeacherModule\Exceptions\NewtonApiException
     * @throws \App\TeacherModule\Exceptions\NewtonApiRequestException
     * @throws \App\TeacherModule\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Nette\Utils\JsonException
     */
    public function validateDiscriminantCond(QuadraticEquationTemplateNP $data): bool
    {
        //bdump('VALIDATE DISCRIMINANT CONDITION');
        $discriminant = $this->getDiscriminantExpression($data->getStandardized(), $data->getVariable());
        $data->setDiscriminant($discriminant);
        $data->setConditionValidateItem('discriminant');

        $matches = $this->conditionService->findConditionsMatches([
            $this->constHelper::DISCRIMINANT => [
                $data->getConditionAccessor() => [
                    'data' => $data
                ]
            ]
        ]);

        if (!$matches) {
            return false;
        }

        $jsonData = Json::encode($matches);
        $this->templateJsonDataFunctionality->create(ArrayHash::from([ 'jsonData' => $jsonData ]), true, $data->getIdHidden(), $data->getConditionType());

        return true;
    }
}
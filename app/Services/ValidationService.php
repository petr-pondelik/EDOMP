<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 17:36
 */

namespace App\Services;

use App\Exceptions\InvalidParameterException;
use App\Exceptions\NewtonApiSyntaxException;
use App\Exceptions\ProblemFinalCollisionException;
use App\Exceptions\ProblemTemplateFormatException;
use App\Exceptions\StringFormatException;
use App\Helpers\ConstHelper;
use App\Helpers\LatexHelper;
use App\Helpers\StringsHelper;
use App\Model\Functionality\TemplateJsonDataFunctionality;
use Doctrine\ORM\Query\Expr\Math;
use Nette\NotSupportedException;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;
use Nette\Utils\Strings;
use NXP\Exception\MathExecutorException;

/**
 * Class ValidationService
 * @package App\Service
 */
class ValidationService
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
     * @var TemplateJsonDataFunctionality
     */
    protected $templateJsonDataFunctionality;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * @var LatexHelper
     */
    protected $latexHelper;

    /**
     * @var array
     */
    protected $validationMapping;

    /**
     * @var array
     */
    protected $validationMessages;

    /**
     * ValidationService constructor.
     * @param NewtonApiClient $newtonApiClient
     * @param MathService $mathService
     * @param ConditionService $conditionService
     * @param TemplateJsonDataFunctionality $templateJsonDataFunctionality
     * @param ConstHelper $constHelper
     * @param StringsHelper $stringsHelper
     * @param LatexHelper $latexHelper
     */
    public function __construct
    (
        NewtonApiClient $newtonApiClient,
        MathService $mathService, ConditionService $conditionService,
        TemplateJsonDataFunctionality $templateJsonDataFunctionality,
        ConstHelper $constHelper, StringsHelper $stringsHelper, LatexHelper $latexHelper
    )
    {
        $this->newtonApiClient = $newtonApiClient;
        $this->mathService = $mathService;
        $this->conditionService = $conditionService;
        $this->templateJsonDataFunctionality = $templateJsonDataFunctionality;
        $this->constHelper = $constHelper;
        $this->stringsHelper = $stringsHelper;
        $this->latexHelper = $latexHelper;

        $this->validationMapping = [

            'username' => static function($filledVal){
                if(empty($filledVal)) {
                    return 0;
                }
                return -1;
            },

            'password' => static function($filledVal){
                if(empty($filledVal)) {
                    return 0;
                }
                return -1;
            },

            //Validate password in administration User section
            'password_confirm' => static function(ArrayHash $filledVal){
                if(empty($filledVal->password) || empty($filledVal->password_confirm)) {
                    return 0;
                }
                if(strcmp($filledVal->password, $filledVal->password_confirm)) {
                    return 0;
                }
                if(strlen($filledVal->password) < 8) {
                    return 1;
                }
                return -1;
            },

            'groups' => static function(ArrayHash $filledVal){
                if(count($filledVal) < 1){
                    return 0;
                }
                return -1;
            },

            'label' => static function($filledVal){
                if(empty($filledVal)){
                    return 0;
                }
                if(strlen($filledVal) > 64){
                    return 1;
                }
                return -1;
            },

            'logo' => static function($filledVal){
                if(empty($filledVal)){
                    return 0;
                }
                return -1;
            },

            'school_year' => static function($filledVal){
                if(empty($filledVal)){
                    return 0;
                }
                if(!Strings::match($filledVal, '~[0-9]{4}(\/|\-)([0-9]{4}|[0-9]{2})~')) {
                    return 1;
                }
                return -1;
            },

            'test_number' => static function($filledVal){
                if(empty($filledVal)){
                    return 0;
                }
                if($filledVal < 0){
                    return 1;
                }
                return -1;
            },

            'test_term' => static function($filledVal){
                if(empty($filledVal)) return 0;
                return -1;
            },

            'success_rate' => static function($filledVAl){
                if(!empty($filledVAl)){
                    $filledVAl = Strings::replace($filledVAl, '~,~', '.');
                    if(!is_numeric($filledVAl)){
                        return 0;
                    }
                    if($filledVAl < 0 || $filledVAl > 1) {
                        return 1;
                    }
                }
                return -1;
            },

            'body' => function($filledVal){
                if(isset($filledVal->variable)) {
                    return $this->validateBody($filledVal->body, $filledVal->bodyType, $filledVal->variable);
                }
                return $this->validateBody($filledVal->body, $filledVal->bodyType);
            },

            'variable' => static function($filledVal){
                if(empty($filledVal)){
                    return 0;
                }
                $matches = Strings::match($filledVal, '~^[a-z]$~');
                if(strlen($filledVal) !== 1 || !$matches || count($matches) !== 1) {
                    return 1;
                }
                return -1;
            },

            'firstN' => static function($filledVal){
                if(empty($filledVal)) {
                    return 0;
                }
                if($filledVal <= 0) {
                    return 1;
                }
                return -1;
            },

            'category' => static function($filledVal){
                if(empty($filledVal)){
                    return 0;
                }
                return -1;
            },

            'subCategory' => static function($filledVal){
                if(empty($filledVal)){
                    return 0;
                }
                return -1;
            },

            'difficulty' => static function($filledVal){
                if(empty($filledVal)){
                    return 0;
                }
                return -1;
            },

            'problemFinalType' => static function($filledVal){

                if(empty($filledVal)){
                    return 0;
                }

                return -1;

            },

            'type' => [

                'type_' . $this->constHelper::LINEAR_EQ => function($filledVal){
                    if(empty($filledVal)){
                        return 0;
                    }
                    if(!$this->validateEquation($filledVal->standardized, $filledVal->variable, $this->constHelper::LINEAR_EQ)){
                        return 1;
                    }
                    return -1;
                },

                'type_' . $this->constHelper::QUADRATIC_EQ => function($filledVal){
                    if(empty($filledVal)){
                        return 0;
                    }
                    if(!$this->validateEquation($filledVal->standardized, $filledVal->variable, $this->constHelper::QUADRATIC_EQ)){
                        return 1;
                    }
                    return -1;
                },

                'type_' . $this->constHelper::ARITHMETIC_SEQ => function($filledVal){
                    if(empty($filledVal)){
                        return 0;
                    }
                    if(!$this->validateSequence($filledVal->body, $filledVal->standardized, $filledVal->variable, $this->constHelper::ARITHMETIC_SEQ)){
                        return 1;
                    }
                    return -1;
                },

                'type_' . $this->constHelper::GEOMETRIC_SEQ => function($filledVal){
                    if(empty($filledVal)){
                        return 0;
                    }
                    if(!$this->validateSequence($filledVal->body, $filledVal->standardized, $filledVal->variable, $this->constHelper::GEOMETRIC_SEQ)){
                        return 1;
                    }
                    return -1;
                }

            ],

            'condition' => [

                'condition_' . $this->constHelper::RESULT => function(ArrayHash $filledVal, $problemId = null){
                    $parametersInfo = $this->stringsHelper::extractParametersInfo($filledVal->body);

                    // Maximal number of parameters exceeded
                    if($parametersInfo->count > $this->constHelper::PARAMETERS_MAX) {
                        return 2;
                    }

                    // Maximal parameters complexity exceeded
                    if($parametersInfo->complexity > $this->constHelper::COMPLEXITY_MAX) {
                        return 3;
                    }

                    if(!$this->validateResultCond($filledVal->accessor, $filledVal->standardized, $filledVal->variable, $parametersInfo, $problemId)) {
                        return 4;
                    }

                    return -1;
                },

                'condition_' . $this->constHelper::DISCRIMINANT => function(ArrayHash $filledVal, $problemId = null){

                    bdump('VALIDATE DISCRIMINANT CONDITION');
                    bdump($filledVal);

                    $parametersInfo = $this->stringsHelper::extractParametersInfo($filledVal->body);

                    //Maximal number of parameters exceeded
                    if($parametersInfo->count > $this->constHelper::PARAMETERS_MAX) {
                        return 2;
                    }

                    //Maximal parameters complexity exceeded
                    if($parametersInfo->complexity > $this->constHelper::COMPLEXITY_MAX) {
                        return 3;
                    }

                    if(!$this->validateDiscriminantCond(
                        $filledVal->accessor, $filledVal->standardized, $filledVal->variable, $parametersInfo, $problemId)) {
                        return 4;
                    }

                    return -1;
                }

            ],

            'conditions_valid' => static function(int $filledVal){
                if(!$filledVal) {
                    return 0;
                }
                return -1;
            },

            'role' => static function($filledVal){
                if(!$filledVal){
                    return 0;
                }
                return -1;
            },

            'superGroup' => static function($filledVal){
                if(!$filledVal){
                    return 0;
                }
                return -1;
            }

        ];

        $this->validationMessages = [

            'username' => [
                0 => 'Zadejte uživatelské jméno.'
            ],

            'password' => [
                0 => 'Zadejte heslo.'
            ],

            'password_confirm' => [
                0 => 'Obě hesla musí být vyplněna a shodovat se.',
                1 => 'Heslo musí mít délku alespoň 8 znaků.'
            ],


            'groups' => [
                0 => 'Zvolte alespoň jednu skupinu.'
            ],

            'label' => [
                0 => 'Název musí být vyplněn.',
                1 => 'Název nesmí být delší než 64 znaků.'
            ],

            'logo' => [
                0 => 'Zvolte prosím logo.'
            ],

            'school_year' => [
                0 => 'Školní rok musí bý vyplněn.',
                1 => 'Školní roku musí být ve formátu rrrr/rr(rr) nebo rrrr-rr(rr).'
            ],

            'test_number' => [
                0 => 'Číslo testu musí být vyplněno.',
                1 => 'Číslo testu nesmí být záporné.'
            ],

            'test_term' => [
                0 => 'Období testu musí být vyplněno.'
            ],

            'success_rate' => [
                0 => 'Úspěšnost úlohy musí být číselná hodnota.',
                1 => 'Úspěšnost úlohy musí být v intervalu <0; 1>'
            ],

            'body' => [
                0 => 'Tělo úlohy musí být vyplněno.',
                1 => 'Vstupní LaTeX musí být uvnitř značek pro matematický mód.',
                2 => 'Šablona neobsahuje zadanou neznámou.',
                3 => 'Šablona není validní matematický výraz.'
            ],

            'variable' => [
                0 => 'Zadejte prosím neznámou.',
                1 => 'Zadejte prosím právě jedno malé písmo abecedy.'
            ],

            'firstN' => [
                0 => 'Zvolte počet prvních členů.',
                1 => 'Počet prvních členů musí být kladný.'
            ],

            'category' => [

                0 => 'Zvolte prosím kategorii.'

            ],

            'subCategory' => [

                0 => 'Zvolte prosím podkategorii.'

            ],

            'difficulty' => [

                0 => 'Zvolte prosím obtížnost.'

            ],

            'problemFinalType' => [

                0 => 'Zvolte prosím typ úlohy.'

            ],

            'type' => [

                'type_' . $this->constHelper::LINEAR_EQ => [
                    0 => 'Zvolte prosím typ úlohy.',
                    1 => 'Zadaná úloha není lineární rovnicí.'
                ],

                'type_' . $this->constHelper::QUADRATIC_EQ => [
                    0 => 'Zvolte prosím typ úlohy.',
                    1 => 'Zadaná úloha není kvadratickou rovnicí.'
                ],

                'type_' . $this->constHelper::ARITHMETIC_SEQ => [
                    0 => 'Zvolte prosím typ úlohy.',
                    1 => 'Zadaná úloha není aritmetickou posloupností.'
                ],

                'type_' . $this->constHelper::GEOMETRIC_SEQ => [
                    0 => 'Zvolte prosím typ úlohy.',
                    1 => 'Zadaná úloha není geometrickou posloupností.'
                ]

            ],

            'condition' => [

                'condition_' . $this->constHelper::RESULT => [
                    0 => 'Struktura musí být vyplněna',
                    1 => 'Chybný formát vstupního LaTeXu',
                    2 => 'Překročen povolený počet parametrů. (maximálně ' . $this->constHelper::PARAMETERS_MAX . ')',
                    3 => 'Překročena povolená složitost parametrů. (maximálně ' . $this->constHelper::COMPLEXITY_MAX . ')',
                    4 => 'Podmínka není splnitelná.'
                ],

                'condition_' . $this->constHelper::DISCRIMINANT => [
                    0 => 'Struktura úlohy musí být vyplněna.',
                    1 => 'Chybný formát vstupního LaTeXu.',
                    2 => 'Překročen povolený počet parametrů. (maximálně ' . $this->constHelper::PARAMETERS_MAX . ')',
                    3 => 'Překročena povolená složitost parametrů. (maximálně ' . $this->constHelper::COMPLEXITY_MAX . ')',
                    4 => 'Podmínka není splnitelná.'
                ],

            ],

            'conditions_valid' => [
                0 => 'Některou ze zadaných podmínek nelze splnit.'
            ],

            'role' => [
                0 => 'Zvolte prosím roli.'
            ],

            'superGroup' => [
                0 => 'Zvolte prosím superskupinu.'
            ]

        ];
    }

    /**
     * @param string $body
     * @param int $type
     * @param string|null $variable
     * @return int
     * @throws InvalidParameterException
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function validateBody(string $body, int $type,  string $variable = null): int
    {
        if(empty($body)){
            return 0;
        }

        if($type !== $this->constHelper::BODY_FINAL){

            if(!$this->latexHelper::latexWrapped($body)){
                return 1;
            }

            $parsed = $this->latexHelper::parseLatex($body);

            //Validation over the parameters
            $this->validateParameters($parsed);

            $split = $this->stringsHelper::splitByParameters($parsed);

            if(empty($variable) || !$this->stringsHelper::containsVariable($split, $variable)){
                return 2;
            }

            $parametrized = $this->stringsHelper::getParametrized($parsed);

            try{
                $this->newtonApiClient->simplify($parametrized->expression);
            } catch (NewtonApiSyntaxException $e){
                return 3;
            }

        }

        return -1;
    }

    /**
     * @param string $expression
     * @throws InvalidParameterException
     */
    private function validateParameters(string $expression): void
    {
        $split = $this->stringsHelper::splitByParameters($expression, true);

        if(count($split) <= 1){
            throw new InvalidParameterException('Zadaná šablona neobsahuje parametr.');
        }

        foreach($split as $part){
            if($part !== '' && Strings::startsWith($part, '<par')){
                if(!Strings::match($part, '~<par min="[0-9]+" max="[0-9]+"/>~')){
                    throw new InvalidParameterException('Zadaná šablona obsahuje nevalidní parametr.');
                }
                else{
                    $min = $this->stringsHelper::extractParAttr($part, 'min');
                    $max = $this->stringsHelper::extractParAttr($part, 'max');
                    if($min > $max){
                        throw new InvalidParameterException('Neplatný interval parametru.');
                    }
                }
            }
        }

    }

    /**
     * @param string $standardized
     * @param string $variable
     * @param int $eqType
     * @return bool
     */
    public function validateEquation(string $standardized, string $variable, int $eqType): bool
    {
        switch($eqType){
            case $this->constHelper::LINEAR_EQ:
                return $this->validateLinearEquation($standardized, $variable);
            case $this->constHelper::QUADRATIC_EQ:
                return $this->validateQuadraticEquation($standardized, $variable);
        }
        return false;
    }

    /**
     * @param string $standardized
     * @param string $variable
     * @return bool
     */
    public function validateLinearEquation(string $standardized, string $variable): bool
    {
        bdump('VALIDATE LINEAR EQUATION');
        bdump($standardized);

        // Remove all the spaces
        $standardized = $this->stringsHelper::removeWhiteSpaces($standardized);

        // Trivial fail case
        if(Strings::match($standardized, '~' . $variable . '\^' . '~')){
            return false;
        }

        // Match string against the linear expression regexp
        $matches = Strings::match($standardized, '~' . $this->stringsHelper::getLinearEquationRegExp($variable) . '~');

        // Check if the whole expression was matched
        return $matches[0] === $standardized;
    }

    /**
     * @param string $standardized
     * @param string $variable
     * @return bool
     */
    public function validateQuadraticEquation(string $standardized, string $variable): bool
    {
        bdump('VALIDATE QUADRATIC EQUATION');

        // Remove all the spaces
        $standardized = $this->stringsHelper::removeWhiteSpaces($standardized);
        bdump($standardized);

        // Match string against the quadratic expression regexp
        $matches = Strings::match($standardized, '~' . $this->stringsHelper::getQuadraticEquationRegExp($variable) . '~');

        // Check if the whole expression was matched
        return $matches[0] === $standardized;
    }

    /**
     * @param string $expression
     * @param string $standardized
     * @param string $variable
     * @param int $seqType
     * @return bool
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validateSequence(string $expression, string $standardized, string $variable, int $seqType): bool
    {
        bdump('VALIDATE SEQUENCE');
        bdump($standardized);

        if(!$this->stringsHelper::isSequence($this->latexHelper::parseLatex($expression), $variable)){
            return false;
        }

        $params = [];
        $parametersInfo = $this->stringsHelper::extractParametersInfo($expression);
        for($i = 0; $i < $parametersInfo->count; $i++){
            $params['p' . $i] = ($i+2);
        }

        $final = $this->stringsHelper::passValues($standardized, $params);

        switch ($seqType){
            case $this->constHelper::ARITHMETIC_SEQ:
                return $this->validateArithmeticSequence($final, $variable);
            case $this->constHelper::GEOMETRIC_SEQ:
                return $this->validateGeometricSequence($final, $variable);
        }

        return false;
    }

    /**
     * @param string $final
     * @param string $variable
     * @return bool
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validateArithmeticSequence(string $final, string $variable): bool
    {
        bdump('VALIDATE ARITHMETIC SEQUENCE');
        bdump($final);
        $final = $this->newtonApiClient->simplify($final);
        $final = $this->stringsHelper::nxpFormat($final, $variable);

        $a1 = $this->stringsHelper::passValues($final, [ $variable => 1 ]);
        $a2 = $this->stringsHelper::passValues($final, [ $variable => 2 ]);
        $a3 = $this->stringsHelper::passValues($final, [ $variable => 3 ]);

        bdump($a1);
        bdump($a2);
        bdump($a3);

        $diff1 = $this->mathService->evaluateExpression('(' . $a2 . ')' . ' - ' . '(' . $a1 . ')');
        bdump($diff1);
        $diff2 = $this->mathService->evaluateExpression('(' . $a3 . ')' . ' - ' . '(' . $a2 . ')');
        bdump($diff2);

        return round($diff1, 2) === round($diff2,2);
    }

    /**
     * @param string $expression
     * @param string $variable
     * @return bool
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validateGeometricSequence(string $expression, string $variable): bool
    {
        bdump('Validate geometric sequence');
        $expression = $this->newtonApiClient->simplify($expression);
        $expression = $this->stringsHelper::nxpFormat($expression, $variable);

        $a1 = $this->stringsHelper::passValues($expression, [ $variable => 1 ]);
        $a2 = $this->stringsHelper::passValues($expression, [ $variable => 2 ]);
        $a3 = $this->stringsHelper::passValues($expression, [ $variable => 3 ]);

        $quot1 = $this->mathService->evaluateExpression('(' . $a2 . ')' . '/' . '(' . $a1 . ')');
        $quot2 = $this->mathService->evaluateExpression('(' . $a3 . ')' . '/' . '(' . $a2 . ')');

        return round($quot1, 2) === round($quot2, 2);
    }

    /**
     * @param int $accessor
     * @param string $standardized
     * @param string $variable
     * @param ArrayHash $parametersInfo
     * @param null $problemId
     * @return bool
     * @throws \Nette\Utils\JsonException
     * @throws ProblemTemplateFormatException
     */
    public function validateResultCond
    (
        int $accessor, string $standardized, string $variable, ArrayHash $parametersInfo, $problemId = null
    ): bool
    {
        $variableExp = $this->stringsHelper::getLinearVariableExpresion($standardized, $variable);

        try{
            $matches = $this->conditionService->findConditionsMatches([
                $this->constHelper::RESULT => [
                    $accessor => [
                        'parametersInfo' => $parametersInfo,
                        'variableExp' => $variableExp
                    ]
                ]
            ]);
        } catch (MathExecutorException $e){
            throw new ProblemTemplateFormatException('Zadán chybný formát šablony.');
        }

        if(!$matches){
            return false;
        }

        $jsonData = Json::encode($matches);
        $this->templateJsonDataFunctionality->create(ArrayHash::from([
            'jsonData' => $jsonData
        ]), $problemId);

        return true;
    }

    /**
     * @param int $accessor
     * @param string $standardized
     * @param string $variable
     * @param ArrayHash $parametersInfo
     * @param null $problemId
     * @return bool
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Nette\Utils\JsonException
     */
    private function validateDiscriminantCond
    (
        int $accessor, string $standardized, string $variable, ArrayHash $parametersInfo, $problemId = null
    ): bool
    {
        bdump('VALIDATE DISCRIMINANT COND');
        bdump($standardized);
        $discriminantExp = $this->mathService->getDiscriminantExpression($standardized, $variable);
        bdump($discriminantExp);

        $matches = $this->conditionService->findConditionsMatches([
            $this->constHelper::DISCRIMINANT => [
                $accessor => [
                    'parametersInfo' => $parametersInfo,
                    'discriminantExp' => $discriminantExp
                ]
            ]
        ]);

        if(!$matches){
            return false;
        }

        $jsonData = Json::encode($matches);
        $this->templateJsonDataFunctionality->create(ArrayHash::from([
            'jsonData' => $jsonData
        ]), $problemId);

        return true;
    }

    /**
     * @param $fields
     * @return array
     */
    public function validate($fields): array
    {
        $validationErrors = [];

        foreach((array)$fields as $key1 => $value1){

            if(!array_key_exists($key1, $this->validationMapping)){
                throw new NotSupportedException('Požadavek obsahuje neočekávanou hodnotu.');
            }

            if(is_array($value1)){
                foreach ($value1 as $key2 => $value2){
                    if(!array_key_exists($key2, $this->validationMapping[$key1])){
                        throw new NotSupportedException('Požadavek obsahuje neočekávanou hodnotu.');
                    }
                    if( ($validationRes = $this->validationMapping[$key1][$key2]($value2)) !== -1 ){
                        $validationErrors[$key1][] = $this->validationMessages[$key1][$key2][$validationRes];
                    }
                }
            }
            else if( ($validationRes = $this->validationMapping[$key1]($value1)) !== -1 ){
                $validationErrors[$key1][] = $this->validationMessages[$key1][$validationRes];
            }
        }

        return $validationErrors;
    }

    /**
     * @param $fields
     * @param $problemId
     * @return array
     */
    public function conditionValidate($fields, $problemId = null): array
    {
        // Based on the problemId presence, it will be decided it to update or to create

        $validationErrors = [];

        foreach((array)$fields as $key1 => $value1){

            if(!array_key_exists($key1, $this->validationMapping)){
                throw new NotSupportedException('Požadavek obsahuje neočekávanou hodnotu.');
            }

            if(is_array($value1)){
                foreach ($value1 as $key2 => $value2){
                    if(!array_key_exists($key2, $this->validationMapping[$key1])){
                        throw new NotSupportedException('Požadavek obsahuje neočekávanou hodnotu.');
                    }
                    if( ($validationRes = $this->validationMapping[$key1][$key2]($value2, $problemId)) !== -1){ // Zde se liší!
                        $validationErrors[$key1][] = $this->validationMessages[$key1][$key2][$validationRes];
                    }
                }
            }
            else if( ($validationRes = $this->validationMapping[$key1]($value1, $problemId)) !== -1 ){ // Zde se liší!
                    $validationErrors[$key1][] = $this->validationMessages[$key1][$validationRes];
            }

        }

        return $validationErrors;
    }

}
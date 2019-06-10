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
use App\Exceptions\StringFormatException;
use App\Helpers\ConstHelper;
use App\Helpers\LatexHelper;
use App\Helpers\StringsHelper;
use App\Model\Functionality\TemplateJsonDataFunctionality;
use Nette\NotSupportedException;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;
use Nette\Utils\Strings;

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
     * @var ConditionMatchingService
     */
    protected $conditionMatchingService;

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
     * @param ConditionMatchingService $conditionMatchingService
     * @param TemplateJsonDataFunctionality $templateJsonDataFunctionality
     * @param ConstHelper $constHelper
     * @param StringsHelper $stringsHelper
     * @param LatexHelper $latexHelper
     */
    public function __construct
    (
        NewtonApiClient $newtonApiClient,
        MathService $mathService, ConditionMatchingService $conditionMatchingService,
        TemplateJsonDataFunctionality $templateJsonDataFunctionality,
        ConstHelper $constHelper, StringsHelper $stringsHelper, LatexHelper $latexHelper
    )
    {
        $this->newtonApiClient = $newtonApiClient;
        $this->mathService = $mathService;
        $this->conditionMatchingService = $conditionMatchingService;
        $this->templateJsonDataFunctionality = $templateJsonDataFunctionality;
        $this->constHelper = $constHelper;
        $this->stringsHelper = $stringsHelper;
        $this->latexHelper = $latexHelper;

        $this->validationMapping = [

            'username' => static function($filledVal){
                if(empty($filledVal))
                    return 0;
                return -1;
            },

            'password' => static function($filledVal){
                if(empty($filledVal))
                    return 0;
                return -1;
            },

            //Validate password in administration User section
            'password_confirm' => static function(ArrayHash $filledVal){
                if(empty($filledVal->password) || empty($filledVal->password_confirm))
                    return 0;
                if(strcmp($filledVal->password, $filledVal->password_confirm))
                    return 0;
                if(strlen($filledVal->password) < 8)
                    return 1;
                return -1;
            },

            'groups' => static function(ArrayHash $filledVal){
                if(count($filledVal) < 1)
                    return 0;
                return -1;
            },

            'label' => static function($filledVal){
                if(empty($filledVal)) return 0;
                return -1;
            },

            'logo_file' => static function($filledVal){
                if(empty($filledVal)) return 0;
                return -1;
            },

            'school_year' => static function($filledVal){
                if(empty($filledVal)) return 0;
                if(!Strings::match($filledVal, '~[0-9]{4}(\/|\-)([0-9]{4}|[0-9]{2})~'))
                    return 1;
                return -1;
            },

            'test_number' => static function($filledVal){
                if(empty($filledVal)) return 0;
                if($filledVal < 0) return 1;
                return -1;
            },

            'test_term' => static function($filledVal){
                if(empty($filledVal)) return 0;
                return -1;
            },

            'success_rate' => static function($filledVAl){
                if(!empty($filledVAl)){
                    if(!is_numeric($filledVAl)) return 0;
                    if($filledVAl < 0 || $filledVAl > 1)
                        return 1;
                }
                return -1;
            },

            'body' => function($filledVal){
                if(isset($filledVal->variable))
                    return $this->validateBody($filledVal->body, $filledVal->bodyType, $filledVal->variable);
                return $this->validateBody($filledVal->body, $filledVal->bodyType);
            },

            'variable' => static function($filledVal){
                if(empty($filledVal))
                    return 0;
                $matches = Strings::match($filledVal, '~^[a-z]$~');
                if(strlen($filledVal) !== 1 || !$matches || count($matches) !== 1)
                    return 1;
                return -1;
            },

            'first_n' => static function($filledVal){
                if(empty($filledVal))
                    return 0;
                if($filledVal <= 0)
                    return 1;
                return -1;
            },

            'type' => [

                'type_' . $this->constHelper::LINEAR_EQ => function($filledVal){
                    if(!$this->validateEquation($this->latexHelper::parseLatex($filledVal->body), $filledVal->standardized, $filledVal->variable, $this->constHelper::LINEAR_EQ))
                        return 0;
                    return -1;
                },

                'type_' . $this->constHelper::QUADRATIC_EQ => function($filledVal){
                    if(!$this->validateEquation($this->latexHelper::parseLatex($filledVal->body), $filledVal->standardized, $filledVal->variable, $this->constHelper::QUADRATIC_EQ))
                        return 0;
                    return -1;
                },

                'type_' . $this->constHelper::ARITHMETIC_SEQ => function($filledVal){
                    if(!$this->validateSequence($this->latexHelper::parseLatex($filledVal->body), $filledVal->variable, $this->constHelper::ARITHMETIC_SEQ)) return 0;
                    return -1;
                },

                'type_' . $this->constHelper::GEOMETRIC_SEQ => function($filledVal){
                    if(!$this->validateSequence($this->latexHelper::parseLatex($filledVal->body), $filledVal->variable, $this->constHelper::GEOMETRIC_SEQ)) return 0;
                    return -1;
                }

            ],

            'condition' => [

                'condition_' . $this->constHelper::RESULT => function(ArrayHash $filledVal, $problemId = null){
                    $parametersInfo = $this->stringsHelper::extractParametersInfo($filledVal->body);

                    //Maximal number of parameters exceeded
                    if($parametersInfo->count > $this->constHelper::PARAMETERS_MAX)
                        return 2;

                    //Maximal parameters complexity exceeded
                    if($parametersInfo->complexity > $this->constHelper::COMPLEXITY_MAX)
                        return 3;

                    if(!$this->validateResultCond($filledVal->accessor, $filledVal->body, $filledVal->standardized, $filledVal->variable, $parametersInfo, $problemId))
                        return 4;

                    return -1;
                },

                'condition_' . $this->constHelper::DISCRIMINANT => function(ArrayHash $filledVal, $problemId = null){
                    $parametersInfo = $this->stringsHelper::extractParametersInfo($filledVal['body']);

                    //Maximal number of parameters exceeded
                    if($parametersInfo->count > $this->constHelper::PARAMETERS_MAX)
                        return 2;

                    //Maximal parameters complexity exceeded
                    if($parametersInfo->complexity > $this->constHelper::COMPLEXITY_MAX)
                        return 3;

                    if(!$this->validateDiscriminantCond(
                        $filledVal->accessor, $filledVal->body, $filledVal->variable, $parametersInfo, $problemId)) {
                        return 4;
                    }

                    return -1;
                }

            ],

            'conditions_valid' => static function(int $filledVal){
                if(!$filledVal)
                    return 0;
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
                0 => 'Název musí být vyplněn.'
            ],

            'logo_file' => [
                0 => 'Soubor musí být zvolen.'
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

            'first_n' => [
                0 => 'Zvolte počet prvních členů.',
                1 => 'Počet prvních členů musí být kladný.'
            ],

            'type' => [

                'type_' . $this->constHelper::LINEAR_EQ => [
                    0 => 'Zadaná úloha není lineární rovnicí.'
                ],

                'type_' . $this->constHelper::QUADRATIC_EQ => [
                    0 => 'Zadaná úloha není kvadratickou rovnicí.'
                ],

                'type_' . $this->constHelper::ARITHMETIC_SEQ => [
                    0 => 'Zadaná úloha není aritmetickou posloupností.'
                ],

                'type_' . $this->constHelper::GEOMETRIC_SEQ => [
                    0 => 'Zadaná úloha není geometrickou posloupností.'
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
        if(empty($body)) return 0;

        if($type !== $this->constHelper::BODY_FINAL){

            if(!$this->latexHelper::latexWrapped($body)) return 1;

            $parsed = $this->latexHelper::parseLatex($body);

            //Validation over the parameters
            $this->validateParameters($parsed);

            $split = $this->stringsHelper::splitByParameters($parsed);

            if(empty($variable) || !$this->stringsHelper::containsVariable($split, $variable))
                return 2;

            $parametrized = $this->stringsHelper::getParametrized($parsed);

            $parsedNewton = $this->stringsHelper::newtonFormat($parametrized->expression);

            try{
                $this->newtonApiClient->simplify($parsedNewton);
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
     * @param string $expression
     * @param string $standardized
     * @param string $variable
     * @param int $eqType
     * @return bool
     */
    public function validateEquation(string $expression, string $standardized, string $variable, int $eqType): bool
    {
        if(!$this->stringsHelper::isEquation($expression))
            return false;
        switch($eqType){
            case $this->constHelper::LINEAR_EQ: return $this->validateLinearEquation($standardized, $variable);
            case $this->constHelper::QUADRATIC_EQ: return $this->validateQuadraticEquation($standardized, $variable);
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
        if(Strings::contains($standardized, $variable . '^'))
            return false;
        return true;
    }

    /**
     * @param string $standardized
     * @param string $variable
     * @return bool
     */
    public function validateQuadraticEquation(string $standardized, string $variable): bool
    {
        if(!Strings::contains($standardized, $variable . '^2'))
            return false;
        if(Strings::match($standardized, '~' . $variable . '\^[3-9]~'))
            return false;
        return true;
    }

    /**
     * @param string $expression
     * @param string $variable
     * @param int $seqType
     * @return bool
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validateSequence(string $expression, string $variable, int $seqType): bool
    {
        $parametrized = $this->stringsHelper::getParametrized($expression);
        $parametersInfo = $this->stringsHelper::extractParametersInfo($expression);
        $expression = $parametrized->expression;

        if(!$this->stringsHelper::isSequence($expression, $variable)) return false;

        try{
            $sides = $this->stringsHelper::getEquationSides($expression, false);
        } catch (StringFormatException $e){
            return false;
        }

        $params = [];
        for($i = 0; $i < $parametersInfo->count; $i++)
            $params['p' . $i] = 1;

        $final = $this->stringsHelper::passValues($sides->right, $params);

        switch ($seqType){
            case $this->constHelper::ARITHMETIC_SEQ:    return $this->validateArithmeticSequence($final, $variable);
            case $this->constHelper::GEOMETRIC_SEQ:     return $this->validateGeometricSequence($final, $variable);
        }

        return false;
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
    public function validateArithmeticSequence(string $expression, string $variable): bool
    {
        $expression = $this->newtonApiClient->simplify($this->stringsHelper::newtonFormat($expression));
        $expression = $this->stringsHelper::nxpFormat($expression, $variable);

        $a1 = $this->stringsHelper::passValues($expression, [ $variable => 1 ]);
        $a2 = $this->stringsHelper::passValues($expression, [ $variable => 2 ]);
        $a3 = $this->stringsHelper::passValues($expression, [ $variable => 3 ]);

        $diff1 = $this->mathService->evaluateExpression('(' . $a2 . ')' . ' - ' . '(' . $a1 . ')');
        $diff2 = $this->mathService->evaluateExpression('(' . $a3 . ')' . ' - ' . '(' . $a2 . ')');

        return $diff1 == $diff2;
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
        $expression = $this->newtonApiClient->simplify($this->stringsHelper::newtonFormat($expression));
        $expression = $this->stringsHelper::nxpFormat($expression, $variable);

        $a1 = $this->stringsHelper::passValues($expression, [ $variable => 1 ]);
        $a2 = $this->stringsHelper::passValues($expression, [ $variable => 2 ]);
        $a3 = $this->stringsHelper::passValues($expression, [ $variable => 3 ]);

        $quot1 = $this->mathService->evaluateExpression('(' . $a2 . ')' . '/' . '(' . $a1 . ')');
        $quot2 = $this->mathService->evaluateExpression('(' . $a3 . ')' . '/' . '(' . $a2 . ')');

        return $quot1 == $quot2;
    }

    /**
     * @param int $accessor
     * @param string $body
     * @param string $standardized
     * @param string $variable
     * @param ArrayHash $parametersInfo
     * @param null $problemId
     * @return bool
     * @throws \Nette\Utils\JsonException
     */
    public function validateResultCond(int $accessor, string $body, string $standardized, string $variable, ArrayHash $parametersInfo, $problemId = null): bool
    {
        $variableExp = $this->stringsHelper::getLinearVariableExpresion($standardized, $variable);

        $matches = $this->conditionMatchingService->findConditionsMatches([
            $this->constHelper::RESULT => [
                $accessor => [
                    'parametersInfo' => $parametersInfo,
                    'variableExp' => $variableExp
                ]
            ]
        ]);

        if(!$matches) return false;

        $arrayToJson['matches'] = $matches;

        $jsonData = Json::encode($arrayToJson);
        $this->templateJsonDataFunctionality->create(ArrayHash::from([
            'jsonData' => $jsonData
        ]), $problemId);

        return true;
    }

    /**
     * @param int $accessor
     * @param string $body
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
    private function validateDiscriminantCond(int $accessor, string $body, string $variable, ArrayHash $parametersInfo, $problemId = null): bool
    {
        $parametrized = $this->stringsHelper::getParametrized($body);
        $discriminantExp = $this->mathService->getDiscriminantExpression($parametrized->expression, $variable);

        $matches = $this->conditionMatchingService->findConditionsMatches([
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

        $arrayToJson['matches'] = $matches;

        $jsonData = Json::encode($arrayToJson);
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
                        throw  new NotSupportedException('Požadavek obsahuje neočekávanou hodnotu.');
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
    public function editValidate($fields, $problemId): array
    {
        $validationErrors = [];

        foreach((array)$fields as $key1 => $value1){

            if(!array_key_exists($key1, $this->validationMapping))
                throw new NotSupportedException('Požadavek obsahuje neočekávanou hodnotu.');

            if(is_array($value1)){
                foreach ($value1 as $key2 => $value2){
                    if(!array_key_exists($key2, $this->validationMapping[$key1]))
                        throw  new NotSupportedException('Požadavek obsahuje neočekávanou hodnotu.');
                    if( ($validationRes = $this->validationMapping[$key1][$key2]($value2, $problemId)) !== -1 )
                        $validationErrors[$key1][] = $this->validationMessages[$key1][$key2][$validationRes];
                }
            }
            else{
                if( ($validationRes = $this->validationMapping[$key1]($value1, $problemId)) !== -1 )
                    $validationErrors[$key1][] = $this->validationMessages[$key1][$validationRes];
            }

        }

        return $validationErrors;
    }

}
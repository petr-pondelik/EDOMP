<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 3.4.19
 * Time: 18:18
 */

namespace App\Services;


use App\Helpers\ConstHelper;
use App\Helpers\StringsHelper;
use Nette\NotSupportedException;
use Nette\Utils\ArrayHash;

/**
 * Class ConditionMatchingService
 * @package App\Services
 */
class ConditionMatchingService
{

    /**
     * @var MathService
     */
    protected $mathService;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * @var array
     */
    protected $conditionsMatches;

    /**
     * @var array
     */
    protected $validationFunctions;

    /**
     * ConditionMatchingService constructor.
     * @param MathService $mathService
     * @param StringsHelper $stringsHelper
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        MathService $mathService,
        StringsHelper $stringsHelper, ConstHelper $constHelper
    )
    {
        $this->mathService = $mathService;
        $this->stringsHelper = $stringsHelper;
        $this->constHelper = $constHelper;

        $this->conditionsMatches = [

            ($this->constHelper::RESULT) => [

                ($this->constHelper::RESULT_POSITIVE) => function($values){
                    return $this->findMatches(
                        $values["parametersInfo"],
                        $values["variableExp"],
                        $this->constHelper::RESULT,
                        $this->constHelper::RESULT_POSITIVE
                    );
                },

                ($this->constHelper::RESULT_ZERO) => function($values){
                    return $this->findMatches(
                        $values["parametersInfo"],
                        $values["variableExp"],
                        $this->constHelper::RESULT,
                        $this->constHelper::RESULT_ZERO
                    );
                },

                ($this->constHelper::RESULT_NEGATIVE) => function($values){
                    return $this->findMatches(
                        $values["parametersInfo"],
                        $values["variableExp"],
                        $this->constHelper::RESULT,
                        $this->constHelper::RESULT_NEGATIVE
                    );
                }

            ],

            ($this->constHelper::DISCRIMINANT) => [

                ($this->constHelper::DISCRIMINANT_POSITIVE) => function($values){
                    return $this->findMatches(
                            $values["parametersInfo"],
                            $values["discriminantExp"],
                            $this->constHelper::DISCRIMINANT,
                            $this->constHelper::DISCRIMINANT_POSITIVE
                        );
                },

                ($this->constHelper::DISCRIMINANT_ZERO) => function($values){
                    return $this->findMatches(
                        $values["parametersInfo"],
                        $values["discriminantExp"],
                        $this->constHelper::DISCRIMINANT,
                        $this->constHelper::DISCRIMINANT_ZERO
                    );
                },

                ($this->constHelper::DISCRIMINANT_NEGATIVE) => function($values){
                    return $this->findMatches(
                        $values["parametersInfo"],
                        $values["discriminantExp"],
                        $this->constHelper::DISCRIMINANT,
                        $this->constHelper::DISCRIMINANT_NEGATIVE
                    );
                },

                ($this->constHelper::DISCRIMINANT_INTEGER) => function($values){
                    return $this->findMatches(
                        $values["parametersInfo"],
                        $values["discriminantExp"],
                        $this->constHelper::DISCRIMINANT,
                        $this->constHelper::DISCRIMINANT_INTEGER
                    );
                },

                ($this->constHelper::DISCRIMINANT_POSITIVE_SQUARE) => function($values){
                    return $this->findMatches(
                        $values["parametersInfo"],
                        $values["discriminantExp"],
                        $this->constHelper::DISCRIMINANT,
                        $this->constHelper::DISCRIMINANT_POSITIVE_SQUARE
                    );
                }

            ],

        ];

        $this->validationFunctions = [

            ($this->constHelper::RESULT) => [

                ($this->constHelper::RESULT_POSITIVE) => function($value){
                    return $value > 0;
                },

                ($this->constHelper::RESULT_ZERO) => function($value){
                    return $value == 0;
                },

                ($this->constHelper::RESULT_NEGATIVE) => function($value){
                    return $value < 0;
                }

            ],

            ($this->constHelper::DISCRIMINANT) => [

                ($this->constHelper::DISCRIMINANT_POSITIVE) => function($value){
                    return $value > 0;
                },

                ($this->constHelper::DISCRIMINANT_ZERO) => function($value){
                    return $value == 0;
                },

                ($this->constHelper::DISCRIMINANT_NEGATIVE) => function($value){
                    return $value < 0;
                },

                ($this->constHelper::DISCRIMINANT_INTEGER) => function($value){
                    return is_int($value);
                },

                ($this->constHelper::DISCRIMINANT_POSITIVE_SQUARE) => function($value){
                    if($value <= 0) return false;
                    $squareRoot = sqrt($value);
                    $squareRootInt = (int) $squareRoot;
                    return ($squareRootInt == $squareRoot);
                }

            ]

        ];
    }

    /**
     * @param $fields
     * @return array
     */
    public function findConditionsMatches($fields)
    {
        $result = [];
        foreach((array)$fields as $keyType => $value){
            if(!array_key_exists($keyType, $this->conditionsMatches))
                throw new NotSupportedException('Nepodporovaný typ podmínky.');
            foreach((array)$value as $keyAccessor => $item){
                if(!array_key_exists($keyAccessor, $this->conditionsMatches[$keyType]))
                    throw new NotSupportedException('Nepodporovaná podmínka.');
                $result = $this->conditionsMatches[$keyType][$keyAccessor]($item);
            }
        }
        return $result;
    }

    /**
     * @param ArrayHash $parametersInfo
     * @param string $expression
     * @param int $typeAccessor
     * @param int $accessor
     * @return array|bool
     */
    private function findMatches(ArrayHash $parametersInfo, string $expression, int $typeAccessor, int $accessor)
    {
        $matches = [];
        $matchesCnt = 0;
        $res = false;

        bdump($expression);

        if($parametersInfo->count === 1){
            for($i = $parametersInfo["minMax"][0]["min"]; $i <= $parametersInfo["minMax"][0]["max"]; $i++){
                $final = $this->stringsHelper::passValues($expression, [
                    "p0" =>  $i
                ]);
                if( $this->validationFunctions[$typeAccessor][$accessor]($this->mathService->evaluateExpression($final)) ){
                    $matches[$matchesCnt++] = [
                        'p0' => $i
                    ];
                    $res = true;
                }
            }
        }
        else if($parametersInfo->count === 2){
            for($i = $parametersInfo["minMax"][0]["min"]; $i <= $parametersInfo["minMax"][0]["max"]; $i++){
                for($j = $parametersInfo["minMax"][1]["min"]; $j <= $parametersInfo["minMax"][1]["max"]; $j++){
                    $final = $this->stringsHelper::passValues($expression, [
                        "p0" => $i,
                        "p1" => $j
                    ]);
                    if( $this->validationFunctions[$typeAccessor][$accessor]($this->mathService->evaluateExpression($final)) ){
                        $matches[$matchesCnt++] = [
                            "p0" => $i,
                            "p1" => $j
                        ];
                        $res = true;
                    }
                }
            }
        }
        else if($parametersInfo->count === 3){
            for($i = $parametersInfo["minMax"][0]["min"]; $i <= $parametersInfo["minMax"][0]["max"]; $i++){
                for($j = $parametersInfo["minMax"][1]["min"]; $j <= $parametersInfo["minMax"][1]["max"]; $j++){
                    for($k = $parametersInfo["minMax"][2]["min"]; $k <= $parametersInfo["minMax"][2]["max"]; $k++){
                        $final = $this->stringsHelper::passValues($expression, [
                            "p0" => $i,
                            "p1" => $j,
                            "p2" => $k
                        ]);
                        if( $this->validationFunctions[$typeAccessor][$accessor]($this->mathService->evaluateExpression($final)) ){
                            $matches[$matchesCnt++] = [
                                "p0" => $i,
                                "p1" => $j,
                                "p2" => $k
                            ];
                            $res = true;
                        }
                    }
                }
            }
        }
        else if($parametersInfo->count === 4){
            for($i = $parametersInfo["minMax"][0]["min"]; $i <= $parametersInfo["minMax"][0]["max"]; $i++){
                for($j = $parametersInfo["minMax"][1]["min"]; $j <= $parametersInfo["minMax"][1]["max"]; $j++){
                    for($k = $parametersInfo["minMax"][2]["min"]; $k <= $parametersInfo["minMax"][2]["max"]; $k++){
                        for($l = $parametersInfo["minMax"][3]["min"]; $l <= $parametersInfo["minMax"][3]["max"]; $l++){
                            $final = $this->stringsHelper::passValues($expression, [
                                "p0" => $i,
                                "p1" => $j,
                                "p2" => $k,
                                "p3" => $l
                            ]);
                            if( $this->validationFunctions[$typeAccessor][$accessor]($this->mathService->evaluateExpression($final)) ){
                                $matches[$matchesCnt++] = [
                                    "p0" => $i,
                                    "p1" => $j,
                                    "p2" => $k,
                                    "p3" => $l
                                ];
                                $res = true;
                            }
                        }
                    }
                }
            }
        }
        else if($parametersInfo->count === 5){
            for($i = $parametersInfo["minMax"][0]["min"]; $i <= $parametersInfo["minMax"][0]["max"]; $i++){
                for($j = $parametersInfo["minMax"][1]["min"]; $j <= $parametersInfo["minMax"][1]["max"]; $j++){
                    for($k = $parametersInfo["minMax"][2]["min"]; $k <= $parametersInfo["minMax"][2]["max"]; $k++){
                        for($l = $parametersInfo["minMax"][3]["min"]; $l <= $parametersInfo["minMax"][3]["max"]; $l++){
                            for($m = $parametersInfo["minMax"][4]["min"]; $m <= $parametersInfo["minMax"][4]["max"]; $m++){
                                $final = $this->stringsHelper::passValues($expression, [
                                    "p0" => $i,
                                    "p1" => $j,
                                    "p2" => $k,
                                    "p3" => $l,
                                    "p4" => $m,
                                ]);
                                if( $this->validationFunctions[$typeAccessor][$accessor]($this->mathService->evaluateExpression($final)) ){
                                    $matches[$matchesCnt++] = [
                                        "p0" => $i,
                                        "p1" => $j,
                                        "p2" => $k,
                                        "p3" => $l,
                                        "p4" => $m
                                    ];
                                    $res = true;
                                }
                            }
                        }
                    }
                }
            }
        }
        else if($parametersInfo->count === 6){
            for($i = $parametersInfo["minMax"][0]["min"]; $i <= $parametersInfo["minMax"][0]["max"]; $i++){
                for($j = $parametersInfo["minMax"][1]["min"]; $j <= $parametersInfo["minMax"][1]["max"]; $j++){
                    for($k = $parametersInfo["minMax"][2]["min"]; $k <= $parametersInfo["minMax"][2]["max"]; $k++){
                        for($l = $parametersInfo["minMax"][3]["min"]; $l <= $parametersInfo["minMax"][3]["max"]; $l++){
                            for($m = $parametersInfo["minMax"][4]["min"]; $m <= $parametersInfo["minMax"][4]["max"]; $m++){
                                for($n = $parametersInfo["minMax"][5]["min"]; $n <= $parametersInfo["minMax"][5]["max"]; $n++){
                                    $final = $this->stringsHelper::passValues($expression, [
                                        "p0" => $i,
                                        "p1" => $j,
                                        "p2" => $k,
                                        "p3" => $l,
                                        "p4" => $m,
                                        "p5" => $n
                                    ]);
                                    if( $this->validationFunctions[$typeAccessor][$accessor]($this->mathService->evaluateExpression($final)) ){
                                        $matches[$matchesCnt++] = [
                                            "p0" => $i,
                                            "p1" => $j,
                                            "p2" => $k,
                                            "p3" => $l,
                                            "p4" => $m,
                                            "p5" => $n
                                        ];
                                        $res = true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if(!$res) return false;

        return $matches;
    }
}
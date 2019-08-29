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
use App\Model\NonPersistent\Entity\ArithmeticSequenceTemplateNP;
use App\Model\NonPersistent\Entity\GeometricSequenceTemplateNP;
use App\Model\NonPersistent\Entity\LinearEquationTemplateNP;
use App\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\Model\NonPersistent\Entity\QuadraticEquationTemplateNP;
use App\Model\Persistent\Repository\ProblemConditionTypeRepository;
use Nette\NotSupportedException;
use Nette\Utils\Json;
use Nette\Utils\Strings;

/**
 * Class ConditionService
 * @package App\Services
 */
class ConditionService
{
    protected const RE_VAR_COEFFICIENT = '~(\([\dp\+\-\*\(\)\/\^\s]+|-?\s?[\d\/p\s]+\)?)\s(x\^?\d*)~';

    /**
     * @var MathService
     */
    protected $mathService;

    /**
     * @var ProblemConditionTypeRepository
     */
    protected $problemConditionTypeRepository;

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
    protected $validationMapping;

    /**
     * @var array
     */
    protected $validationFunctions;

    /**
     * ConditionService constructor.
     * @param MathService $mathService
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param StringsHelper $stringsHelper
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        MathService $mathService, ProblemConditionTypeRepository $problemConditionTypeRepository,
        StringsHelper $stringsHelper, ConstHelper $constHelper
    )
    {
        $this->mathService = $mathService;
        $this->stringsHelper = $stringsHelper;
        $this->constHelper = $constHelper;

        $this->validationFunctions = [

            'linearEquationType' => static function (LinearEquationTemplateNP $data, array $parValuesArr) use ($stringsHelper, $mathService) {
//                bdump('CONDITION SERVICE: LINEAR EQUATION TYPE');
                $final = $stringsHelper::passValues($data->getStandardized(), $parValuesArr);
                $varCoefficients = Strings::matchAll($final, self::RE_VAR_COEFFICIENT);
                foreach ($varCoefficients as $varCoefficient) {
                    $coefficientRes = $mathService->evaluateExpression($varCoefficient[1]);
                    if($coefficientRes === 0.0 && $varCoefficient[2] === 'x'){
                        return false;
                    }
                    if($coefficientRes !== 0.0 && $varCoefficient[2] !== 'x'){
                        return false;
                    }
                }
//                try {
//                    $mathService->evaluateExpression($final);
////                    $this->parser::solve($data->data);
//                } catch (\Exception $e) {
//                    bdump($e->getMessage());
//                    return false;
//                }
//                bdump('TRUE');
                return true;
            },

            'quadraticEquationType' => static function (QuadraticEquationTemplateNP $data, array $parValuesArr) use ($stringsHelper, $mathService) {

//                bdump('VALIDATE QUADRATIC EQUATION TYPE');

                $final = $stringsHelper::passValues($data->getStandardized(), $parValuesArr);
                $varCoefficients = Strings::matchAll($final, self::RE_VAR_COEFFICIENT);
//                bdump($varCoefficients);

                // TODO: VALIDATE COLLECTED CONDITIONS FOR PARAMETERS!!!

//                $skip = false;
//
//                if ($data->isSkipZeroFractions()) {
//                    foreach ($data->getGlobalDivider()->getOriginalFactors() as $originalFactor) {
//                        if (!Strings::contains($originalFactor, $data->getVariable())) {
//                            $filled = $stringsHelper::passValues($originalFactor, $parValuesArr);
//                            if ($mathService->evaluateExpression($filled) === 0.0) {
//                                $skip = true;
//                                break;
//                            }
//                        }
//                    }
//                }
//
//                if ($skip) {
//                    return false;
//                }

                foreach ($varCoefficients as $varCoefficient) {
//                    bdump($varCoefficient);
                    $coefficientRes = $mathService->evaluateExpression($varCoefficient[1]);
                    if ($coefficientRes === 0.0 && $varCoefficient[2] === 'x^2') {
                        return false;
                    }
                    if ($coefficientRes !== 0.0 && $varCoefficient[2] !== 'x^2' && $varCoefficient[2] !== 'x') {
                        return false;
                    }
                }

                return true;
            },

            'arithmeticSequenceType' => function (ArithmeticSequenceTemplateNP $data) {
                try {
                    $values = Json::decode($values);
                    $diff1 = $this->mathService->evaluateExpression('(' . $values[1] . ')' . ' - ' . '(' . $values[0] . ')');
                    $diff2 = $this->mathService->evaluateExpression('(' . $values[2] . ')' . ' - ' . '(' . $values[1] . ')');
                    return round($diff1, 5) === round($diff2, 5);
                } catch (\Exception $e) {
                    return false;
                }
            },

            'geometricSequenceType' => function (GeometricSequenceTemplateNP $data) {
                try {
                    $values = Json::decode($values);
                    $values[0] = $this->mathService->evaluateExpression($values[0]);
                    $values[1] = $this->mathService->evaluateExpression($values[1]);
                    $values[2] = $this->mathService->evaluateExpression($values[2]);
                    // If the sequence contains 0 --> check all values for zero value --> if all values aren't zero, return false
                    if ($values[0] === 0 || $values[1] === 0 || $values[2] === 0) {
                        return !($values[0] !== 0 || $values[1] !== 0 || $values[2] !== 0);
                    }
                    $quot1 = $this->mathService->evaluateExpression('(' . $values[1] . ')' . '/' . '(' . $values[0] . ')');
                    $quot2 = $this->mathService->evaluateExpression('(' . $values[2] . ')' . '/' . '(' . $values[1] . ')');
                    return round($quot1, 5) === round($quot2, 5);
                } catch (\Exception $e) {
                    return false;
                }
            },

            'positive' => static function (ProblemTemplateNP $data, array $parValuesArr) use ($stringsHelper, $mathService) {
                $final = $stringsHelper::passValues($data->getConditionValidateData(), $parValuesArr);
                try {
                    $res = $mathService->evaluateExpression($final);
                    return $res > 0;
//                    return $this->mathService->evaluateExpression($value) > 0;
                } catch (\Exception $e) {
                    return false;
                }
            },

            'zero' => static function (ProblemTemplateNP $data, array $parValuesArr) use ($stringsHelper, $mathService) {
                $final = $stringsHelper::passValues($data->getConditionValidateData(), $parValuesArr);
                try {
                    return $mathService->evaluateExpression($final) === 0.0;
                } catch (\Exception $e) {
                    return false;
                }
            },

            'negative' => static function (ProblemTemplateNP $data, array $parValuesArr) use ($stringsHelper, $mathService) {
                $final = $stringsHelper::passValues($data->getConditionValidateData(), $parValuesArr);
                try {
                    $res = $mathService->evaluateExpression($final);
                    return $res < 0;
//                    return $this->mathService->evaluateExpression($value) < 0;
                } catch (\Exception $e) {
                    return false;
                }
            },

            'positiveSquare' => static function (ProblemTemplateNP $data, array $parValuesArr) use ($stringsHelper, $mathService) {
                $final = $stringsHelper::passValues($data->getConditionValidateData(), $parValuesArr);
                try {
                    $value = $mathService->evaluateExpression($final);
                    if ($value <= 0) {
                        return false;
                    }
                    $squareRoot = sqrt($value);
                    $squareRootInt = (int)$squareRoot;
                    return $squareRootInt == $squareRoot;
                } catch (\Exception $e) {
                    return false;
                }
            },

            'integer' => static function (ProblemTemplateNP $data, array $parValuesArr) use ($stringsHelper, $mathService) {
                $final = $stringsHelper::passValues($data->getConditionValidateData(), $parValuesArr);
                try {
                    $res = $mathService->evaluateExpression($final);
                    $resInt = (int)$res;
                    return $res == $resInt;
                } catch (\Exception $e) {
                    return false;
                }
            },

        ];

        $problemConditionTypes = $problemConditionTypeRepository->findAll();

        // Create association array of validation callbacks based on the conditions from DB
        foreach ($problemConditionTypes as $problemConditionType) {
            $problemConditionTypeID = $problemConditionType->getId();
            foreach ($problemConditionType->getProblemConditions()->getValues() as $problemCondition) {
                $accessor = $problemCondition->getAccessor();
                $this->conditionsMatches[$problemConditionTypeID][$accessor] = function ($data) use ($problemConditionTypeID, $accessor) {
                    return $this->findMatches(
                        $data['data'],
                        $problemConditionTypeID,
                        $accessor
                    );
                };
                if ($validationFunction = $problemCondition->getValidationFunction()) {
                    $this->validationMapping[$problemConditionTypeID][$accessor] = $this->validationFunctions[$validationFunction->getLabel()];
                }
            }
        }

    }

    /**
     * @param $fields
     * @return array|null
     */
    public function findConditionsMatches($fields): ?array
    {
        bdump('FIND CONDITIONS MATCHES');
        $result = [];
        foreach ((array)$fields as $keyType => $value) {
            if (!array_key_exists($keyType, $this->conditionsMatches)) {
                throw new NotSupportedException('Nepodporovaný typ podmínky.');
            }
            foreach ((array)$value as $keyAccessor => $item) {
                if (!array_key_exists($keyAccessor, $this->conditionsMatches[$keyType])) {
                    throw new NotSupportedException('Nepodporovaná podmínka.');
                }
                $result = $this->conditionsMatches[$keyType][$keyAccessor]($item);
            }
        }
        return $result;
    }

    /**
     * @param ProblemTemplateNP $data
     * @param int $typeAccessor
     * @param int $accessor
     * @return array|null
     */
    private function findMatches(ProblemTemplateNP $data, int $typeAccessor, int $accessor): ?array
    {
        bdump('FIND MATCHES');
        $matches = [];
        $matchesCnt = 0;
        $res = false;

        $minMax = $data->getParametersData()->getMinMax();
        $parCount = $data->getParametersData()->getCount();

        if ($parCount === 1) {
            for ($i = $minMax[0]['min']; $i <= $minMax[0]['max']; $i++) {
                $parValuesArr = ['p0' => $i];
                if ($this->validationMapping[$typeAccessor][$accessor]($data, $parValuesArr)) {
                    $matches[$matchesCnt++] = $parValuesArr;
                    $res = true;
                }
            }
        } else if ($parCount === 2) {
            for ($i = $minMax[0]['min']; $i <= $minMax[0]['max']; $i++) {
                for ($j = $minMax[1]['min']; $j <= $minMax[1]['max']; $j++) {
                    $parValuesArr = ['p0' => $i, 'p1' => $j];
                    if ($this->validationMapping[$typeAccessor][$accessor]($data, $parValuesArr)) {
                        $matches[$matchesCnt++] = $parValuesArr;
                        $res = true;
                    }
                }
            }
        } else if ($parCount === 3) {
            for ($i = $minMax[0]['min']; $i <= $minMax[0]['max']; $i++) {
                for ($j = $minMax[1]['min']; $j <= $minMax[1]['max']; $j++) {
                    for ($k = $minMax[2]['min']; $k <= $minMax[2]['max']; $k++) {
                        $parValuesArr = ['p0' => $i, 'p1' => $j, 'p2' => $k];
                        if ($this->validationMapping[$typeAccessor][$accessor]($data, $parValuesArr)) {
                            $matches[$matchesCnt++] = $parValuesArr;
                            $res = true;
                        }
                    }
                }
            }
        } else if ($parCount === 4) {
            for ($i = $minMax[0]['min']; $i <= $minMax[0]['max']; $i++) {
                for ($j = $minMax[1]['min']; $j <= $minMax[1]['max']; $j++) {
                    for ($k = $minMax[2]['min']; $k <= $minMax[2]['max']; $k++) {
                        for ($l = $minMax[3]['min']; $l <= $minMax[3]['max']; $l++) {
                            $parValuesArr = ['p0' => $i, 'p1' => $j, 'p2' => $k, 'p3' => $l];
                            if ($this->validationMapping[$typeAccessor][$accessor]($data, $parValuesArr)) {
                                $matches[$matchesCnt++] = $parValuesArr;
                                $res = true;
                            }
                        }
                    }
                }
            }
        } else if ($parCount === 5) {
            for ($i = $minMax[0]['min']; $i <= $minMax[0]['max']; $i++) {
                for ($j = $minMax[1]['min']; $j <= $minMax[1]['max']; $j++) {
                    for ($k = $minMax[2]['min']; $k <= $minMax[2]['max']; $k++) {
                        for ($l = $minMax[3]['min']; $l <= $minMax[3]['max']; $l++) {
                            for ($m = $minMax[4]['min']; $m <= $minMax[4]['max']; $m++) {
                                $parValuesArr = ['p0' => $i, 'p1' => $j, 'p2' => $k, 'p3' => $l, 'p4' => $m];
                                if ($this->validationMapping[$typeAccessor][$accessor]($data, $parValuesArr)) {
                                    $matches[$matchesCnt++] = $parValuesArr;
                                    $res = true;
                                }
                            }
                        }
                    }
                }
            }
        } else if ($parCount === 6) {
            for ($i = $minMax[0]['min']; $i <= $minMax[0]['max']; $i++) {
                for ($j = $minMax[1]['min']; $j <= $minMax[1]['max']; $j++) {
                    for ($k = $minMax[2]['min']; $k <= $minMax[2]['max']; $k++) {
                        for ($l = $minMax[3]['min']; $l <= $minMax[3]['max']; $l++) {
                            for ($m = $minMax[4]['min']; $m <= $minMax[4]['max']; $m++) {
                                for ($n = $minMax[5]['min']; $n <= $minMax[5]['max']; $n++) {
                                    $parValuesArr = ['p0' => $i, 'p1' => $j, 'p2' => $k, 'p3' => $l, 'p4' => $m, 'p5' => $n];
                                    if ($this->validationMapping[$typeAccessor][$accessor]($data, $parValuesArr)) {
                                        $matches[$matchesCnt++] = $parValuesArr;
                                        $res = true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        bdump($matches);

        if (!$res) {
            return null;
        }

        return $matches;
    }
}
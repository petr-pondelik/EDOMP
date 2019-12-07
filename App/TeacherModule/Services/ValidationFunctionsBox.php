<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.11.19
 * Time: 11:34
 */

namespace App\TeacherModule\Services;

use App\CoreModule\Exceptions\DataBoxException;
use App\CoreModule\Helpers\StringsHelper;
use App\CoreModule\Interfaces\IDataBox;
use App\TeacherModule\Model\NonPersistent\Entity\ArithmeticSequenceTemplateNP;
use App\TeacherModule\Model\NonPersistent\Entity\GeometricSequenceTemplateNP;
use App\TeacherModule\Model\NonPersistent\Entity\LinearEquationTemplateNP;
use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\TeacherModule\Model\NonPersistent\Entity\QuadraticEquationTemplateNP;

/**
 * Class ValidationFunctionsBox
 * @package App\TeacherModule\Services
 */
class ValidationFunctionsBox implements IDataBox
{
    /**
     * @var MathService
     */
    protected $mathService;

    /**
     * @var ParameterParser
     */
    protected $parameterParser;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * @var array
     */
    protected $data;

    /**
     * ValidationFunctionsBox constructor.
     * @param MathService $mathService
     * @param ParameterParser $parameterParser
     * @param StringsHelper $stringsHelper
     */
    public function __construct
    (
        MathService $mathService,
        ParameterParser $parameterParser,
        StringsHelper $stringsHelper
    )
    {
        $this->mathService = $mathService;
        $this->parameterParser = $parameterParser;
        $this->stringsHelper = $stringsHelper;

        $this->data = [

            'linearEquationType' => static function (LinearEquationTemplateNP $data, array $parValuesArr) use ($stringsHelper, $mathService, $parameterParser) {

                $varCoefficients = $mathService->extractVariableCoefficients($data, $parValuesArr);

                try {
                    $final = $parameterParser->passValues($data->getLinearVariableExpression(), $parValuesArr);
                    $mathService->evaluateExpression($final);
                } catch (\Exception $e) {
                    return false;
                }

                foreach ($varCoefficients as $varCoefficient) {
                    try {
                        $coefficientRes = $mathService->evaluateExpression($varCoefficient[1]);
                    } catch (\Exception $e) {
                        return false;
                    }
                    if ($coefficientRes === 0.0 && $varCoefficient[2] === '') {
                        return false;
                    }
                    if ($coefficientRes !== 0.0 && $varCoefficient[2] !== '') {
                        return false;
                    }
                }

                foreach ($data->getVarFractionsParametrized() as $parametrizedFraction) {
                    $conditions = $parametrizedFraction->getNonDegradeConditions();
                    foreach ($conditions as $condition) {
                        $final = $stringsHelper::normalizeOperators($parameterParser->passValues($condition->getExpression(), $parValuesArr));
                        try {
                            $res = $mathService->evaluateExpression($final);
                        } catch (\Exception $e) {
                            return false;
                        }
                        if ($res === 0.0) {
                            return false;
                        }
                    }
                }

                foreach ($data->getNonDegradeConditions() as $condition) {
                    $final = $stringsHelper::normalizeOperators($parameterParser->passValues($condition->getExpression(), $parValuesArr));
                    try {
                        $res = $mathService->evaluateExpression($final);
                    } catch (\Exception $e) {
                        return false;
                    }
                    if ($res === 0.0) {
                        return false;
                    }
                }

                return true;
            },

            'quadraticEquationType' => static function (QuadraticEquationTemplateNP $data, array $parValuesArr) use ($stringsHelper, $mathService, $parameterParser) {

                $varCoefficients = $mathService->extractVariableCoefficients($data, $parValuesArr, false);

                foreach ($varCoefficients as $varCoefficient) {
                    try {
                        $coefficientRes = $mathService->evaluateExpression($varCoefficient[1]);
                    } catch (\Exception $e) {
                        return false;
                    }
                    if ($coefficientRes === 0.0 && $varCoefficient[2] === '2') {
                        return false;
                    }
                    if ($coefficientRes !== 0.0 && $varCoefficient[2] !== '2') {
                        return false;
                    }
                }

                foreach ($data->getVarFractionsParametrized() as $parametrizedFraction) {
                    $conditions = $parametrizedFraction->getNonDegradeConditions();
                    foreach ($conditions as $condition) {
                        $final = $stringsHelper::normalizeOperators($parameterParser->passValues($condition->getExpression(), $parValuesArr));
                        try {
                            $res = $mathService->evaluateExpression($final);
                        } catch (\Exception $e) {
                            return false;
                        }
                        if ($res === 0.0) {
                            return false;
                        }
                    }
                }

                foreach ($data->getNonDegradeConditions() as $condition) {
                    $final = $stringsHelper::normalizeOperators($parameterParser->passValues($condition->getExpression(), $parValuesArr));
                    try {
                        $res = $mathService->evaluateExpression($final);
                    } catch (\Exception $e) {
                        return false;
                    }
                    if ($res === 0.0) {
                        return false;
                    }
                }

                return true;
            },

            'arithmeticSequenceType' => function (ArithmeticSequenceTemplateNP $data, array $parValuesArr) {

                try {
                    $values = $data->getFirstValues();

                    $final0 = $this->parameterParser->passValues($values[0], $parValuesArr);
                    $final1 = $this->parameterParser->passValues($values[1], $parValuesArr);
                    $final2 = $this->parameterParser->passValues($values[2], $parValuesArr);

                    $diff1 = $this->mathService->evaluateExpression(sprintf('(%s) - (%s)', $final1, $final0));
                    $diff2 = $this->mathService->evaluateExpression(sprintf('(%s) - (%s)', $final2, $final1));
                    return round($diff1, 5) === round($diff2, 5);
                } catch (\Exception $e) {
                    return false;
                }
            },

            'geometricSequenceType' => function (GeometricSequenceTemplateNP $data, array $parValuesArr) {

                try {
                    $values = $data->getFirstValues();

                    $final0 = $this->parameterParser->passValues($values[0], $parValuesArr);
                    $final1 = $this->parameterParser->passValues($values[1], $parValuesArr);
                    $final2 = $this->parameterParser->passValues($values[2], $parValuesArr);

                    $final0 = $this->mathService->evaluateExpression($final0);
                    $final1 = $this->mathService->evaluateExpression($final1);
                    $final2 = $this->mathService->evaluateExpression($final2);

                    // If the sequence contains 0 --> check all values for zero value --> if all values aren't zero, return false
                    if ($values[0] === 0 || $values[1] === 0 || $values[2] === 0) {
                        return !($values[0] !== 0 || $values[1] !== 0 || $values[2] !== 0);
                    }

                    $quot1 = $this->mathService->evaluateExpression(sprintf('(%s) / (%s)', $final1, $final0));
                    $quot2 = $this->mathService->evaluateExpression(sprintf('(%s) / (%s)', $final2, $final1));

                    return round($quot1, 5) === round($quot2, 5);
                } catch (\Exception $e) {
                    return false;
                }
            },

            'positive' => static function (ProblemTemplateNP $data, array $parValuesArr) use ($mathService, $parameterParser) {
                $final = $parameterParser->passValues($data->getConditionValidateData(), $parValuesArr);
                try {
                    $res = $mathService->evaluateExpression($final);
                    return $res > 0.0;
                } catch (\Exception $e) {
                    return false;
                }
            },

            'zero' => static function (ProblemTemplateNP $data, array $parValuesArr) use ($mathService, $parameterParser) {
                $final = $parameterParser->passValues($data->getConditionValidateData(), $parValuesArr);
                try {
                    return $mathService->evaluateExpression($final) === 0.0;
                } catch (\Exception $e) {
                    return false;
                }
            },

            'negative' => static function (ProblemTemplateNP $data, array $parValuesArr) use ($mathService, $parameterParser) {
                bdump('NEGATIVE');
                $final = $parameterParser->passValues($data->getConditionValidateData(), $parValuesArr);
                bdump($final);
                try {
                    $res = $mathService->evaluateExpression($final);
                    return $res < 0.0;
                } catch (\Exception $e) {
                    return false;
                }
            },

            'positiveSquare' => static function (ProblemTemplateNP $data, array $parValuesArr) use ($mathService, $parameterParser) {
                $final = $parameterParser->passValues($data->getConditionValidateData(), $parValuesArr);
                try {
                    $value = $mathService->evaluateExpression($final);
                    if ($value <= 0.0) {
                        return false;
                    }
                    $squareRoot = sqrt($value);
                    $squareRootInt = (int)$squareRoot;
                    return $squareRootInt == $squareRoot;
                } catch (\Exception $e) {
                    return false;
                }
            },

            'integer' => static function (ProblemTemplateNP $data, array $parValuesArr) use ($mathService, $parameterParser) {
                $final = $parameterParser->passValues($data->getConditionValidateData(), $parValuesArr);
                try {
                    $res = $mathService->evaluateExpression($final);
                    $resInt = (int)$res;
                    return $res == $resInt;
                } catch (\Exception $e) {
                    return false;
                }
            },

        ];
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param $key
     * @return callable
     * @throws DataBoxException
     */
    public function getByKey(string $key): callable
    {
        if (!isset($this->data[$key])) {
            throw new DataBoxException('ValidationFunctions DataBox does not contain specified validation function.');
        }
        return $this->data[$key];
    }
}
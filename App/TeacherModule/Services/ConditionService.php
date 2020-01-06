<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 3.4.19
 * Time: 18:18
 */

namespace App\TeacherModule\Services;


use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;
use Nette\NotSupportedException;

/**
 * Class ConditionService
 * @package App\TeacherModule\Services
 */
final class ConditionService
{
    /**
     * @var ValidationFunctionsBox
     */
    protected $validationFunctions;

    /**
     * @var ProblemConditionsMatrix
     */
    protected $problemConditionsMatrix;

    /**
     * @var array
     */
    protected $conditionsMatches;

    /**
     * @var array
     */
    protected $validationMapping;

    /**
     * ConditionService constructor.
     * @param ProblemConditionsMatrix $problemConditionsMatrix
     * @param ValidationFunctionsBox $validationFunctions
     * @throws \App\CoreModule\Exceptions\DataBoxException
     */
    public function __construct
    (
        ProblemConditionsMatrix $problemConditionsMatrix,
        ValidationFunctionsBox $validationFunctions
    )
    {
        $this->validationFunctions = $validationFunctions;
        $this->problemConditionsMatrix = $problemConditionsMatrix;

        foreach ($this->problemConditionsMatrix->getMatrix() as $typeId => $conditions) {
            foreach ($conditions as $condition) {
                $this->conditionsMatches[$typeId][$condition['accessor']] = function ($data) use ($typeId, $condition) {
                    return $this->findMatches($data['data'], $typeId, $condition['accessor']);
                };
                if ($condition['validationFunctionKey']) {
                    $this->validationMapping[$typeId][$condition['accessor']] = $this->validationFunctions->getByKey($condition['validationFunctionKey']);
                }
            }
        }
    }

    /**
     * @param iterable $data
     * @return array|null
     */
    public function findConditionsMatches(iterable $data): ?array
    {
        $result = [];
        foreach ((array)$data as $keyType => $value) {
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
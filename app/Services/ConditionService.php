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
use App\Model\Repository\ProblemConditionTypeRepository;
use jlawrence\eos\Parser;
use Nette\NotSupportedException;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;

/**
 * Class ConditionService
 * @package App\Services
 */
class ConditionService
{
    /**
     * @var Parser
     */
    protected $parser;

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
     * @param Parser $parser
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param StringsHelper $stringsHelper
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        Parser $parser, ProblemConditionTypeRepository $problemConditionTypeRepository,
        StringsHelper $stringsHelper, ConstHelper $constHelper
    )
    {
        $this->parser = $parser;
        $this->stringsHelper = $stringsHelper;
        $this->constHelper = $constHelper;

        $this->validationFunctions = [

            'positive' => function ($value) {
                return $this->parser::solve($value) > 0;
            },

            'zero' => function ($value) {
                return (int) $this->parser::solve($value) === 0;
            },

            'negative' => function ($value) {
                return $this->parser::solve($value) < 0;
            },

            'integer' => function ($value) {
                return is_int($this->parser::solve($value));
            },

            'positiveSquare' => function ($value) {
                $value = $this->parser::solve($value);
                if ($value <= 0) {
                    return false;
                }
                $squareRoot = sqrt($value);
                $squareRootInt = (int)$squareRoot;
                return $squareRootInt == $squareRoot;
            },

            'differenceExists' => function ($values) {
                $values = Json::decode($values);
                $diff1 = $this->parser::solve('(' . $values[1] . ')' . ' - ' . '(' . $values[0] . ')');
                $diff2 = $this->parser::solve('(' . $values[2] . ')' . ' - ' . '(' . $values[1] . ')');
                return round($diff1, 2) === round($diff2, 2);
            },

            'quotientExists' => function ($values) {
                // TODO: If the sequence starts with 0, it fails --> check $values[0] to zero value --> if $values[1] isn't zero too, than return false
                $values = Json::decode($values);
                $values[0] = $this->parser::solve($values[0]);
                $values[1] = $this->parser::solve($values[1]);
                $quot1 = $this->parser::solve('(' . $values[1] . ')' . '/' . '(' . $values[0] . ')');
                $quot2 = $this->parser::solve('(' . $values[2] . ')' . '/' . '(' . $values[1] . ')');
                return round($quot1, 2) === round($quot2, 2);
            }

        ];

        $problemConditionTypes = $problemConditionTypeRepository->findAll();

        // Create association array of validation callbacks based on the conditions from DB
        foreach ($problemConditionTypes as $problemConditionType) {
            $problemConditionTypeID = $problemConditionType->getId();
            foreach ($problemConditionType->getProblemConditions()->getValues() as $problemCondition) {
                $accessor = $problemCondition->getAccessor();
                $this->conditionsMatches[$problemConditionTypeID][$accessor] = function ($values) use ($problemConditionTypeID, $accessor) {
                    return $this->findMatches(
                        $values['parametersInfo'],
                        $values['data'],
                        $problemConditionTypeID,
                        $accessor
                    );
                };
                if ($validationFunction = $problemCondition->getValidationFunction()) {
                    $this->validationMapping[$problemConditionTypeID][$accessor] = $this->validationFunctions[$validationFunction];
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
     * @param ArrayHash $parametersInfo
     * @param $data
     * @param int $typeAccessor
     * @param int $accessor
     * @return array|null
     */
    private function findMatches(ArrayHash $parametersInfo, $data, int $typeAccessor, int $accessor): ?array
    {
        $matches = [];
        $matchesCnt = 0;
        $res = false;

        if ($parametersInfo->count === 1) {
            for ($i = $parametersInfo['minMax'][0]['min']; $i <= $parametersInfo['minMax'][0]['max']; $i++) {
                $final = $this->stringsHelper::passValues($data, [
                    'p0' => $i
                ]);
                if ($this->validationMapping[$typeAccessor][$accessor]($final)) {
                    $matches[$matchesCnt++] = [
                        'p0' => $i
                    ];
                    $res = true;
                }
            }
        } else if ($parametersInfo->count === 2) {
            for ($i = $parametersInfo['minMax'][0]['min']; $i <= $parametersInfo['minMax'][0]['max']; $i++) {
                for ($j = $parametersInfo['minMax'][1]['min']; $j <= $parametersInfo['minMax'][1]['max']; $j++) {
                    $final = $this->stringsHelper::passValues($data, [
                        'p0' => $i,
                        'p1' => $j
                    ]);
                    if ($this->validationMapping[$typeAccessor][$accessor]($final)) {
                        $matches[$matchesCnt++] = [
                            'p0' => $i,
                            'p1' => $j
                        ];
                        $res = true;
                    }
                }
            }
        } else if ($parametersInfo->count === 3) {
            for ($i = $parametersInfo['minMax'][0]['min']; $i <= $parametersInfo['minMax'][0]['max']; $i++) {
                for ($j = $parametersInfo['minMax'][1]['min']; $j <= $parametersInfo['minMax'][1]['max']; $j++) {
                    for ($k = $parametersInfo['minMax'][2]['min']; $k <= $parametersInfo['minMax'][2]['max']; $k++) {
                        $final = $this->stringsHelper::passValues($data, [
                            'p0' => $i,
                            'p1' => $j,
                            'p2' => $k
                        ]);
                        if ($this->validationMapping[$typeAccessor][$accessor]($final)) {
                            $matches[$matchesCnt++] = [
                                'p0' => $i,
                                'p1' => $j,
                                'p2' => $k
                            ];
                            $res = true;
                        }
                    }
                }
            }
        } else if ($parametersInfo->count === 4) {
            for ($i = $parametersInfo['minMax'][0]['min']; $i <= $parametersInfo['minMax'][0]['max']; $i++) {
                for ($j = $parametersInfo['minMax'][1]['min']; $j <= $parametersInfo['minMax'][1]['max']; $j++) {
                    for ($k = $parametersInfo['minMax'][2]['min']; $k <= $parametersInfo['minMax'][2]['max']; $k++) {
                        for ($l = $parametersInfo['minMax'][3]['min']; $l <= $parametersInfo['minMax'][3]['max']; $l++) {
                            $final = $this->stringsHelper::passValues($data, [
                                'p0' => $i,
                                'p1' => $j,
                                'p2' => $k,
                                'p3' => $l
                            ]);
                            if ($this->validationMapping[$typeAccessor][$accessor]($final)) {
                                $matches[$matchesCnt++] = [
                                    'p0' => $i,
                                    'p1' => $j,
                                    'p2' => $k,
                                    'p3' => $l
                                ];
                                $res = true;
                            }
                        }
                    }
                }
            }
        } else if ($parametersInfo->count === 5) {
            for ($i = $parametersInfo['minMax'][0]['min']; $i <= $parametersInfo['minMax'][0]['max']; $i++) {
                for ($j = $parametersInfo['minMax'][1]['min']; $j <= $parametersInfo['minMax'][1]['max']; $j++) {
                    for ($k = $parametersInfo['minMax'][2]['min']; $k <= $parametersInfo['minMax'][2]['max']; $k++) {
                        for ($l = $parametersInfo['minMax'][3]['min']; $l <= $parametersInfo['minMax'][3]['max']; $l++) {
                            for ($m = $parametersInfo['minMax'][4]['min']; $m <= $parametersInfo['minMax'][4]['max']; $m++) {
                                $final = $this->stringsHelper::passValues($data, [
                                    'p0' => $i,
                                    'p1' => $j,
                                    'p2' => $k,
                                    'p3' => $l,
                                    'p4' => $m,
                                ]);
                                if ($this->validationMapping[$typeAccessor][$accessor]($final)) {
                                    $matches[$matchesCnt++] = [
                                        'p0' => $i,
                                        'p1' => $j,
                                        'p2' => $k,
                                        'p3' => $l,
                                        'p4' => $m
                                    ];
                                    $res = true;
                                }
                            }
                        }
                    }
                }
            }
        } else if ($parametersInfo->count === 6) {
            for ($i = $parametersInfo['minMax'][0]['min']; $i <= $parametersInfo['minMax'][0]['max']; $i++) {
                for ($j = $parametersInfo['minMax'][1]['min']; $j <= $parametersInfo['minMax'][1]['max']; $j++) {
                    for ($k = $parametersInfo['minMax'][2]['min']; $k <= $parametersInfo['minMax'][2]['max']; $k++) {
                        for ($l = $parametersInfo['minMax'][3]['min']; $l <= $parametersInfo['minMax'][3]['max']; $l++) {
                            for ($m = $parametersInfo['minMax'][4]['min']; $m <= $parametersInfo['minMax'][4]['max']; $m++) {
                                for ($n = $parametersInfo['minMax'][5]['min']; $n <= $parametersInfo['minMax'][5]['max']; $n++) {
                                    $final = $this->stringsHelper::passValues($data, [
                                        'p0' => $i,
                                        'p1' => $j,
                                        'p2' => $k,
                                        'p3' => $l,
                                        'p4' => $m,
                                        'p5' => $n
                                    ]);
                                    if ($this->validationMapping[$typeAccessor][$accessor]($final)) {
                                        $matches[$matchesCnt++] = [
                                            'p0' => $i,
                                            'p1' => $j,
                                            'p2' => $k,
                                            'p3' => $l,
                                            'p4' => $m,
                                            'p5' => $n
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

        if (!$res) {
            return null;
        }

        return $matches;
    }
}
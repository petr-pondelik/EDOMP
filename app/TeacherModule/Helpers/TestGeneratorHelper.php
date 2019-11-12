<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.9.19
 * Time: 17:58
 */

namespace App\TeacherModule\Helpers;

use App\CoreModule\Model\Persistent\Entity\Test;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\TeacherModule\Model\NonPersistent\Generator\Variant;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;
use Nette\Utils\Strings;

/**
 * Class TestGeneratorHelper
 * @package App\TeacherModule\Helpers
 */
class TestGeneratorHelper
{
    protected const VARIANTS_LABELS = [
        0 => 'A',
        1 => 'B',
        2 => 'C',
        3 => 'D',
        4 => 'E',
        5 => 'F',
        6 => 'G',
        7 => 'H',
        8 => 'I'
    ];

    /**
     * @var ProblemConditionTypeRepository
     */
    protected $problemConditionTypeRepository;

    /**
     * @var array
     */
    protected $problemConditionTypesId;

    /**
     * TestGeneratorHelper constructor.
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     */
    public function __construct(ProblemConditionTypeRepository $problemConditionTypeRepository)
    {
        $this->problemConditionTypeRepository = $problemConditionTypeRepository;
        $this->problemConditionTypesId = $this->problemConditionTypeRepository->findPairs([], 'id');
    }

    /**
     * @param ArrayHash $data
     * @return array
     */
    public static function getVariants(ArrayHash $data): array
    {
        $variants = [];
        for ($i = 0; $i < $data->variantsCnt; $i++) {
            $variants[] = new Variant($i, self::VARIANTS_LABELS[$i]);
        }
        return $variants;
    }

    /**
     * @param int $seq
     * @param ArrayHash $data
     * @param Test|null $original
     * @return array
     * @throws \Nette\Utils\JsonException
     */
    public static function getSelectedProblems(int $seq, ArrayHash $data, Test $original = null): array
    {
        bdump('GET SELECTED PROBLEMS');
        if (!$original) {
            return Json::decode($data['problem' . $seq], Json::FORCE_ARRAY);
        }
        return $original->getFilters()->getValues()[$seq]->getSelectedProblems();
    }

    /**
     * @param int $seq
     * @param ArrayHash $data
     * @param Test|null $original
     * @return array
     */
    public function getProblemFilters(int $seq, ArrayHash $data, Test $original = null): array
    {
        bdump('GET PROBLEM FILTERS');
        if ($original) {
            return $this->getFiltersFromOriginal($seq, $original);
        }
        return $this->getFiltersFromData($seq, $data);
    }

    /**
     * @param int $seq
     * @param ArrayHash $data
     * @return array
     */
    protected function getFiltersFromData(int $seq, ArrayHash $data): array
    {
        bdump('GET FILTERS FROM DATA');
        $filters['isGenerated'] = false;
        $filters['isTemplate'] = $data['isTemplate' . $seq];
        $filters['problemType'] = $data['problemType' . $seq];
        $filters['difficulty'] = $data['difficulty' . $seq];
        $filters['subCategory'] = $data['subCategory' . $seq];
        $filters['conditionType'] = [];
        foreach ($this->problemConditionTypesId as $item) {
            $filters['conditionType'][$item] = [];
            if ($data['conditionType' . $item . $seq]) {
                $filters['conditionType'][$item] = $data['conditionType' . $item . $seq];
            }
        }
        return $filters;
    }

    /**
     * @param int $seq
     * @param Test $original
     * @return array
     */
    protected function getFiltersFromOriginal(int $seq, Test $original): array
    {
        bdump('GET FILTERS FROM ORIGINAL');
        return $original->getFilters()->getValues()[$seq]->getSelectedFilters();
    }

    /**
     * @param ArrayHash $data
     * @param Test|null $test
     * @return ArrayHash
     */
    public static function preprocessTestBasicData(ArrayHash $data, Test $test = null): ArrayHash
    {
        $res = ArrayHash::from([
            'logo' => $data->logo,
            'term' => $data->testTerm,
            'schoolYear' => $data->schoolYear,
            'testNumber' => (int)$data->testNumber,
            'groups' => $data->groups,
            'introductionText' => $data->introductionText,
            // In the case of regenerate, get variantsCnt from regenerated test
            'variantsCnt' => $test ? $test->getVariantsCnt() : $data->variantsCnt,
            'problemsPerVariant' => $data->problemsPerVariant
        ]);

        if ($test) {
            $res->regenerateProblem = new ArrayHash();
            foreach ($data as $key => $val) {
                $problemSeq = Strings::match($key, '~regenerateProblem(\d)~')[1];
                if ($problemSeq !== null) {
                    $res->regenerateProblem->{$problemSeq} = (bool)$val;
                }
            }
        }

        return $res;
    }
}
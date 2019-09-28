<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.9.19
 * Time: 21:48
 */

namespace App\Helpers;

use App\Model\Persistent\Entity\ProblemConditionType;
use App\Model\Persistent\Repository\ProblemConditionTypeRepository;

/**
 * Class FilterTranslator
 * @package App\Helpers
 */
class FilterViewHelper
{
    /**
     * @var ProblemConditionTypeRepository
     */
    protected $problemConditionTypeRepository;

    /**
     * @var ProblemConditionType[]
     */
    protected $problemConditionTypes;

    /**
     * @var array
     */
    protected static $translationMap = [
        'isTemplate' => 'Šablona',
        'problemType' => 'Typ úlohy',
        'difficulty' => 'Obtížnost',
        'subCategory' => 'Téma'
    ];

    /**
     * FilterTranslator constructor.
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @throws \Exception
     */
    public function __construct(ProblemConditionTypeRepository $problemConditionTypeRepository)
    {
        $this->problemConditionTypeRepository = $problemConditionTypeRepository;
        $this->completeTranslationMap();
    }

    protected function completeTranslationMap(): void
    {
        $problemConditionTypes = $this->problemConditionTypeRepository->findAll();
        foreach ($problemConditionTypes as $problemConditionType) {
            if(!$problemConditionType->isValidation()){
                self::$translationMap['conditionType' . $problemConditionType->getId()] = $problemConditionType->getLabel();
            }
        }
    }

    /**
     * @param iterable $filters
     * @return iterable
     */
    public function preprocessFilters(iterable $filters): iterable
    {
        $res = [];
        bdump(self::$translationMap);
        foreach ($filters as $key => $filter) {
//            if(!Strings::match($key, '~conditionType\d~')){
                if(isset(self::$translationMap[$key])){
                    $res[self::$translationMap[$key]] = $filter;
//                }
            }
        }
        return $res;
    }
}
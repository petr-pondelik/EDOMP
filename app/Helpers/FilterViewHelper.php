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
     * @var array
     */
    protected static $translationMap = [
        'isTemplate' => 'Šablona',
//        'problemType' => 'Typ úlohy',
//        'difficulty' => 'Obtížnost',
//        'subCategory' => 'Téma'
    ];

//    protected function completeTranslationMap(): void
//    {
//        $problemConditionTypes = $this->problemConditionTypeRepository->findAll();
//        foreach ($problemConditionTypes as $problemConditionType) {
//            if(!$problemConditionType->isValidation()){
//                self::$translationMap['conditionType' . $problemConditionType->getId()] = $problemConditionType->getLabel();
//            }
//        }
//    }

    /**
     * @param iterable $filters
     * @return iterable
     */
    public function preprocessFilters(iterable $filters): iterable
    {
        $res = [];
        bdump(self::$translationMap);
        foreach ($filters as $key => $filter) {
            if($filter !== null && isset(self::$translationMap[$key])){
                if($filter){
                    $res[self::$translationMap[$key]] = $filter === 1 ? 'Ano' : $filter;
                }
                else{
                    $res[self::$translationMap[$key]] = 'Ne';
                }
            }
        }
        return $res;
    }
}
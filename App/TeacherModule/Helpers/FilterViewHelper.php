<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.9.19
 * Time: 21:48
 */

namespace App\TeacherModule\Helpers;


/**
 * Class FilterTranslator
 * @package App\TeacherModule\Helpers
 */
final class FilterViewHelper
{
    /**
     * @var array
     */
    protected static $translationMap = [
        'isTemplate' => 'Šablona'
    ];

    /**
     * @param iterable $filters
     * @return iterable
     */
    public function preprocessFilters(iterable $filters): iterable
    {
        $res = [];
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
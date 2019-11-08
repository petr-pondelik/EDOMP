<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.9.19
 * Time: 21:48
 */

namespace App\Helpers;


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
        'isTemplate' => 'Šablona'
    ];

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
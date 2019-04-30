<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 19:00
 */

namespace App\Model\Traits;


/**
 * Trait FilterTrait
 * @package App\Model\Traits
 */
trait FilterTrait
{
    /**
     * @param iterable $filters
     * @return array
     */
    public function findFiltered(iterable $filters): array
    {
        $filterArr = [];

        if(isset($filters["problem_type_id"]))
            $filterArr["problemType"] = $filters["problem_type_id"];

        if(isset($filters["difficulty_id"]))
            $filterArr["difficulty"] = $filters["difficulty_id"];

        if(isset($filters["sub_category_id"]))
            $filterArr["subCategory"] = $filters["sub_category_id"];

        return $this->findAssoc($filterArr, "id");
    }
}
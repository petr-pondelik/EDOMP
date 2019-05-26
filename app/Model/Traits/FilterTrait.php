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

        if(isset($filters["is_template"]) && $filters["is_template"] != 0)
            $filterArr["isTemplate"] = $filters["is_template"];

        if(isset($filters["problem_type_id"]) && $filters["problem_type_id"] != 0)
            $filterArr["problemType"] = $filters["problem_type_id"];

        if(isset($filters["difficulty_id"]) && $filters["difficulty_id"] != 0)
            $filterArr["difficulty"] = $filters["difficulty_id"];

        if(isset($filters["sub_category_id"]) && $filters["sub_category_id"] != 0)
            $filterArr["subCategory"] = $filters["sub_category_id"];

        bdump($filterArr);

        return $this->findAssoc($filterArr, "id");
    }
}
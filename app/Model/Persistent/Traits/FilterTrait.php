<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 19:00
 */

namespace App\Model\Persistent\Traits;


use App\Model\Persistent\Entity\Problem;
use App\Model\Persistent\Entity\ProblemType;

/**
 * Trait FilterTrait
 * @package App\Model\Persistent\Traits
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

        //bdump($filters);

        // Apply base filters
        if(isset($filters['is_template']) && $filters['is_template'] !== null){
            $filterArr['isTemplate'] = $filters['is_template'];
        }
        if(isset($filters['problem_type_id']) && count($filters['problem_type_id'])){
            $filterArr['problemType'] = $filters['problem_type_id'];
        }
        if(isset($filters['difficulty_id']) && count($filters['difficulty_id'])){
            $filterArr['difficulty'] = $filters['difficulty_id'];
        }
        if(isset($filters['sub_category_id']) && count($filters['sub_category_id'])){
            $filterArr['subCategory'] = $filters['sub_category_id'];
        }

        $filteredBase = $this->findAssoc($filterArr, 'id');
        //bdump($filteredBase);

        if(isset($filters['problem_type_id'])){

            $problemTypes = $this->createQueryBuilder('er')
                ->select('pt')
                ->from(ProblemType::class, 'pt')
                ->where('pt.id IN (:problemTypesId)')
                ->setParameter('problemTypesId', $filters['problem_type_id'])
                ->getQuery()
                ->getResult();

            // Conjunction of filtered arrays
            $res = $filteredBase;
            $conditionFilter = false;

            // Apply filters by problem conditions
            foreach ($problemTypes as $problemType){

                foreach ($problemType->getConditionTypes()->getValues() as $problemConditionType){

                    if(isset($filters['condition_type_id_' . $problemConditionType->getId()]) && count($filters['condition_type_id_' . $problemConditionType->getId()])) {

                        $conditionFilter = true;

                        $filteredByCondition = $this->createQueryBuilder('er')
                            ->select('p.id')
                            ->from(Problem::class, 'p')
                            ->indexBy('p', 'p.id')
                            ->innerJoin('p.conditions', 'c')
                            ->where('c.problemConditionType = :problemConditionTypeId')
                            ->andWhere('c.accessor IN (:problemConditionAccessor)')
                            ->setParameter('problemConditionTypeId', $problemConditionType->getId())
                            ->setParameter('problemConditionAccessor', $filters['condition_type_id_' . $problemConditionType->getId()])
                            ->getQuery()
                            ->getResult();

                        //bdump($filteredByCondition);

                        foreach ($filteredBase as $key => $item) {
                            if (!isset($filteredByCondition[$key]) && $problemConditionType->getId() === $item->getProblemType()->getId()) {
                                unset($res[$key]);
                            }
                        }
                    }
                }
            }

            //bdump($res);

            if($conditionFilter) {
                return $res;
            }

            //bdump($filteredBase);
            return $filteredBase;
        }

        //bdump($filteredBase);
        return $filteredBase;
    }
}
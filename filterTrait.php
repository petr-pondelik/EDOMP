<?php
///**
// * Created by PhpStorm.
// * User: wiedzmin
// * Date: 30.4.19
// * Time: 19:00
// */
//
//namespace App\Model\Persistent\Traits;
//
//
//use App\Model\Persistent\Entity\Problem;
//use App\Model\Persistent\Entity\ProblemType;
//
///**
// * Trait FilterTrait
// * @package App\Model\Persistent\Traits
// */
//trait FilterTrait
//{
//    /**
//     * @param array $filters
//     * @return array
//     */
//    public static function processFilters(array $filters): array
//    {
//        $res = [];
//        foreach ($filters as $filterKey => $filter) {
//            if ($filterKey !== 'conditionType') {
//                if (is_array($filter)) {
//                    if (count($filter)) {
//                        $res[$filterKey] = $filter;
//                    }
//                } else if ($filter !== null && $filter !== '') {
//                    $res[$filterKey] = $filter;
//                }
//            }
//        }
//        return $res;
//    }
//
//    /**
//     * @param array $filters
//     * @param int $limit
//     * @return array
//     */
//    public function findFiltered(array $filters, int $limit): array
//    {
//        bdump('FIND FILTERED');
//        bdump($filters);
//        bdump($limit);
//
//        $filtersProcessed = self::processFilters($filters);
//        bdump($filtersProcessed);
//
//        $qb = $this->createQueryBuilder('er')
//            ->select('e')
//            ->from($this->_entityName, 'e');
//
//        // Create filter for common attributes
//        foreach ($filtersProcessed as $filterKey => $filterVal) {
//            if (!is_array($filterVal)) {
//                if ($filterVal === false) {
//                    $filterVal = 'false';
//                }
//                $qb->andWhere('e.' . $filterKey . ' = (:filter' . $filterKey . ')')
//                    ->setParameter('filter' . $filterKey, $filterVal);
//            }
//            else {
//                $qb->andWhere('e.' . $filterKey . ' IN (:filter' . $filterKey . ')')
//                    ->setParameter('filter' . $filterKey, $filterVal);
//            }
//        }
//
//        if (isset($filters['problemType'], $filters['conditionType'])) {
//
//            foreach ($filters['conditionType'] as $key => $conditionTypeFilter) {
////                bdump($key);
////                bdump($conditionTypeFilter);
//                $qb->innerJoin('e.conditions', 'c')
//                    ->where('c.problemConditionType = :problemConditionTypeId')
//                    ->andWhere('c.accessor IN (:problemConditionAccessor)')
//                    ->setParameter('problemConditionTypeId', $key)
//                    ->setParameter('problemConditionAccessors', $conditionTypeFilter);
//            }
//
//        }
//
//        $qb->indexBy('e', 'e.id');
////            ->setMaxResults($limit);
//
//        return $qb->getQuery()->getResult();
//
////        $filteredBase = $this->findAssoc($filtersProcessed, 'id');
////
////        if (isset($filters['problemType'])) {
////
////            $problemTypes = $this->createQueryBuilder('er')
////                ->select('pt')
////                ->from(ProblemType::class, 'pt')
////                ->where('pt.id IN (:problemTypes)')
////                ->setParameter('problemTypes', $filters['problemType'])
////                ->getQuery()
////                ->getResult();
////
////            // Conjunction of filtered arrays
////            $res = $filteredBase;
////            $conditionFilter = false;
////
////            // Apply filters by problem conditions
////            foreach ($problemTypes as $problemType) {
////
////                foreach ($problemType->getConditionTypes()->getValues() as $problemConditionType) {
////
////                    if (isset($filters['conditionType'][$problemConditionType->getId()]) && count($filters['conditionType'][$problemConditionType->getId()])) {
////
////                        bdump('FILTERING BY CONDITION');
////
////                        $conditionFilter = true;
////
////                        $filteredByCondition = $this->createQueryBuilder('er')
////                            ->select('p.id')
////                            ->from(Problem::class, 'p')
////                            ->indexBy('p', 'p.id')
////                            ->innerJoin('p.conditions', 'c')
////                            ->where('c.problemConditionType = :problemConditionTypeId')
////                            ->andWhere('c.accessor IN (:problemConditionAccessors)')
////                            ->setParameter('problemConditionTypeId', $problemConditionType->getId())
////                            ->setParameter('problemConditionAccessors', $filters['conditionType'][$problemConditionType->getId()])
////                            ->getQuery()
////                            ->getResult();
////
////                        foreach ($filteredBase as $key => $item) {
////                            if (!isset($filteredByCondition[$key]) && $problemConditionType->getId() === $item->getProblemType()->getId()) {
////                                unset($res[$key]);
////                            }
////                        }
////                    }
////                }
////            }
////
////            if ($conditionFilter) {
////                return $res;
////            }
////
////            return $filteredBase;
////        }
////
////        return $filteredBase;
//    }
//}

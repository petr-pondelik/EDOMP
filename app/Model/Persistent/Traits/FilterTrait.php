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
     * @param array $filters
     * @return array
     */
    public static function processFilters(array $filters): array
    {
        $res = [];
        foreach ($filters as $filterKey => $filter) {
            if ($filterKey !== 'conditionType') {
                if (is_array($filter)) {
                    if (count($filter)) {
                        $res[$filterKey] = $filter;
                    }
                } else if ($filter !== null && $filter !== '') {
                    $res[$filterKey] = $filter;
                }
            }
        }
        return $res;
    }

    /**
     * @param array $filters
     * @param array|null $excludeId
     * @return array
     */
    public function findFiltered(array $filters, array $excludeId = null): array
    {
        bdump('FIND FILTERED');
        bdump($filters);

        $filtersProcessed = self::processFilters($filters);
        bdump($filtersProcessed);

        $filteredBase = $this->findAssoc($filtersProcessed, 'id');

        if (isset($filters['problemType'])) {

            $problemTypes = $this->createQueryBuilder('er')
                ->select('pt')
                ->from(ProblemType::class, 'pt')
                ->where('pt.id IN (:problemTypes)')
                ->setParameter('problemTypes', $filters['problemType'])
                ->getQuery()
                ->getResult();

            // Conjunction of filtered arrays
            $res = $filteredBase;
            $conditionFilter = false;

            // Apply filters by problem conditions
            foreach ($problemTypes as $problemType) {

                foreach ($problemType->getConditionTypes()->getValues() as $problemConditionType) {

                    if (isset($filters['conditionType'][$problemConditionType->getId()]) && count($filters['conditionType'][$problemConditionType->getId()])) {

                        bdump('FILTERING BY CONDITION');

                        $conditionFilter = true;

                        $filteredByCondition = $this->createQueryBuilder('er')
                            ->select('p.id')
                            ->from(Problem::class, 'p')
                            ->indexBy('p', 'p.id')
                            ->innerJoin('p.conditions', 'c')
                            ->where('c.problemConditionType = :problemConditionTypeId')
                            ->andWhere('c.accessor IN (:problemConditionAccessors)')
                            ->setParameter('problemConditionTypeId', $problemConditionType->getId())
                            ->setParameter('problemConditionAccessors', $filters['conditionType'][$problemConditionType->getId()])
                            ->getQuery()
                            ->getResult();

                        foreach ($filteredBase as $key => $item) {
                            if (!isset($filteredByCondition[$key]) && $problemConditionType->getId() === $item->getProblemType()->getId()) {
                                unset($res[$key]);
                            }
                        }
                    }
                }
            }

            if ($conditionFilter) {
                return $res;
            }

            return $filteredBase;
        }

        return $filteredBase;
    }
}

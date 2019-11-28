<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 19:00
 */

namespace App\CoreModule\Model\Persistent\Traits;


use App\CoreModule\Model\Persistent\Entity\Problem;
use App\CoreModule\Model\Persistent\Entity\ProblemType;

/**
 * Trait FilterTrait
 * @package App\CoreModule\Model\Persistent\Traits
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
     * @return array
     */
    public function findFiltered(array $filters): array
    {
        bdump('FIND FILTERED');
        $filtersProcessed = self::processFilters($filters);
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

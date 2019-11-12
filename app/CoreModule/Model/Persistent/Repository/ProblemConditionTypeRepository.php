<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 14:10
 */

namespace App\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemConditionType;

/**
 * Class ProblemConditionTypeRepository
 * @package App\CoreModule\Model\Persistent\Repository
 */
class ProblemConditionTypeRepository extends BaseRepository
{
    /**
     * @param int $problemTypeId
     * @return array
     */
    public function findNonValidation(int $problemTypeId): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('pct')
            ->from(ProblemConditionType::class, 'pct')
            ->innerJoin('pct.problemTypes', 'pt', 'WITH', 'pt.id = :ptID AND pct.isValidation = FALSE')
            ->setParameter('ptID', $problemTypeId);

        return $qb->getQuery()->getResult();
    }
}
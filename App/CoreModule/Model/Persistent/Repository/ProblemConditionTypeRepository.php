<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 14:10
 */

namespace App\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemConditionType;
use App\CoreModule\Model\Persistent\Entity\ProblemType;

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

    /**
     * @return array
     */
    public function findIdAssocByProblemTypes(): array
    {
        $res = [];

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('pt.id as ptId, pt.label as ptLabel, pct.id AS pctId, pct.label AS pctLabel')
            ->from(ProblemType::class, 'pt')
            ->innerJoin('pt.conditionTypes', 'pct', 'WITH', 'pct.isValidation = FALSE');
        $qbRes = $qb->getQuery()->getArrayResult();

        foreach ($qbRes as $qbResItem) {
            $res[$qbResItem['ptId']][] = $qbResItem['pctId'];
        }

        return $res;
    }
}
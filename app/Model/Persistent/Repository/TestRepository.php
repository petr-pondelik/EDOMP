<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 19:16
 */

namespace App\Model\Persistent\Repository;

use App\Model\Persistent\Entity\ProblemFinalTestVariantAssociation;
use App\Model\Persistent\Traits\SequenceValTrait;

/**
 * Class TestRepository
 * @package App\Model\Persistent\Repository
 */
class TestRepository extends BaseRepository
{
    use SequenceValTrait;

    /**
     * @var string
     */
    protected $tableName = 'test';

    /**
     * @param int $id
     * @return array
     */
    public function findVariants(int $id): array
    {
        $qb = $this->createQueryBuilder('t');

        $qb->select('pta.variant')
            ->from(ProblemFinalTestVariantAssociation::class, 'pta')
            ->where('pta.test = :testId')
            ->groupBy('pta.variant')
            ->setParameter('testId', $id);

        return $qb->getQuery()->getResult();
    }
}
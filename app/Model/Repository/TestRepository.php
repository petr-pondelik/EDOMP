<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 19:16
 */

namespace App\Model\Repository;

use Doctrine\ORM\Query\Expr\Join;

/**
 * Class TestRepository
 * @package App\Model\Repository
 */
class TestRepository extends BaseRepository
{
    /**
     * @param int $id
     * @return array
     */
    public function findVariants(int $id): array
    {
        $qb = $this->createQueryBuilder("t");

        $qb->select("pta.variant")
            ->join("App\Model\Entity\ProblemTestAssociation", "pta", Join::WITH, "t.id = :testId")
            ->groupBy("pta.variant")
            ->setParameter("testId", $id);

        return $qb->getQuery()->getResult();
    }
}
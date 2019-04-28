<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:49
 */

namespace App\Model\Repository;

/**
 * Class LinearEqTemplRepository
 * @package App\Model\Repository
 */
class LinearEqTemplRepository extends BaseRepository
{
    /**
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLastId(): int
    {
        $res = $this->createQueryBuilder("er")
            ->setMaxResults(1)
            ->select("er.id")
            ->orderBy("er.id", "DESC")
            ->getQuery()
            ->getSingleResult();

        return $res["id"];
    }
}
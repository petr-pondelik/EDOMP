<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 22:03
 */

namespace App\Model\Repository;

/**
 * Class GroupRepository
 * @package App\Model\Repository
 */
class GroupRepository extends BaseRepository
{
    /**
     * @return mixed
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function findAllowed()
    {
        $qb = $this->createQueryBuilder("g")
            ->select("g")
            ->where("g.id != :id")
            ->indexBy("g", "g.id")
            ->setParameter("id", $this->constHelper::ADMIN_GROUP);

        return $qb->getQuery()->getResult();
    }
}
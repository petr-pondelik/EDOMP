<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.5.19
 * Time: 22:14
 */

namespace App\Model\Repository;

/**
 * Class RoleRepository
 * @package App\Model\Repository
 */
class RoleRepository extends BaseRepository
{
    /**
     * @return mixed
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function findWithoutAdmin()
    {
        $qb = $this->createQueryBuilder("r")
            ->select("r")
            ->where("r.id != :id")
            ->indexBy("r", "r.id")
            ->setParameter("id", $this->constHelper::ADMIN_ROLE);

        return $qb->getQuery()->getResult();
    }
}
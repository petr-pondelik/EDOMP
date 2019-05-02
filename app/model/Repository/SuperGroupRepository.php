<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 22:03
 */

namespace App\Model\Repository;

/**
 * Class SuperGroupRepository
 * @package App\Model\Repository
 */
class SuperGroupRepository extends BaseRepository
{
    /**
     * @return mixed
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function findWithoutAdmin()
    {
        $qb = $this->createQueryBuilder("sg")
            ->select("sg")
            ->where("sg.id != :id")
            ->indexBy("sg", "sg.id")
            ->setParameter("id", $this->constHelper::ADMIN_GROUP);

        return $qb->getQuery()->getResult();
    }
}
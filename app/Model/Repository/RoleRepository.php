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
     * @param bool $teacher
     * @return mixed
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function findAllowed(bool $teacher = false)
    {
        $qb = $this->createQueryBuilder("r")
            ->select("r")
            ->where("r.id != :adminRoleId")
            ->indexBy("r", "r.id")
            ->setParameter("adminRoleId", $this->constHelper::ADMIN_ROLE);

        if($teacher)
            $qb = $qb->andWhere("r.id != :teacherRoleId")
                ->setParameter("teacherRoleId", $this->constHelper::TEACHER_ROLE);

        return $qb->getQuery()->getResult();
    }
}
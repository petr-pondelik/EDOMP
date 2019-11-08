<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.5.19
 * Time: 22:14
 */

namespace App\Model\Persistent\Repository;

use Nette\Security\User;

/**
 * Class RoleRepository
 * @package App\Model\Persistent\Repository
 */
class RoleRepository extends BaseRepository
{
    /**
     * @param User $user
     * @return mixed
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function findAllowed(User $user)
    {
        $qb = $this->createQueryBuilder("r")
            ->select("r")
            ->where("r.id != :adminRoleId")
            ->indexBy("r", "r.id")
            ->setParameter("adminRoleId", $this->constHelper::ADMIN_ROLE);

        if(!$user->isInRole('admin')){
            $qb = $qb->andWhere("r.id != :teacherRoleId")
                ->setParameter("teacherRoleId", $this->constHelper::TEACHER_ROLE);
        }

        return $qb->getQuery()->getResult();
    }
}
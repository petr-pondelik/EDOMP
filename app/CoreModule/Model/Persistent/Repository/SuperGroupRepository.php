<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 22:03
 */

namespace App\CoreModule\Model\Persistent\Repository;

use Nette\Security\User;

/**
 * Class SuperGroupRepository
 * @package App\CoreModule\Model\Persistent\Repository
 */
class SuperGroupRepository extends BaseRepository
{
    /**
     * @param User $user
     * @return mixed
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function findAllowed(User $user)
    {
        $qb = $this->createQueryBuilder("sg")
            ->select("sg")
            ->where("sg.id != :adminId")
            ->indexBy("sg", "sg.id")
            ->setParameter("adminId", $this->constHelper::ADMIN_GROUP);

        if(!$user->isInRole('admin')){
            $qb->andWhere('sg.createdBy = :userId')
                ->setParameter('userId', $user->getId());
        }

        return $qb->getQuery()->getResult();
    }
}
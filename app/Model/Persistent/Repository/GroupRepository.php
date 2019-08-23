<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 22:03
 */

namespace App\Model\Persistent\Repository;

use Nette\Security\User;

/**
 * Class GroupRepository
 * @package App\Model\Persistent\Repository
 */
class GroupRepository extends BaseRepository
{
    /**
     * @param User $user
     * @return mixed
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function findAllowed(User $user)
    {
        $qb = $this->createQueryBuilder('g')
            ->select('g')
            ->where('g.id != :id')
            ->indexBy('g', 'g.id')
            ->setParameter('id', $this->constHelper::ADMIN_GROUP);

        if(!$user->isInRole('admin')){
            $qb->andWhere('g.createdBy = :userId')
                ->setParameter('userId', $user->getId());
        }

        return $qb->getQuery()->getResult();
    }
}
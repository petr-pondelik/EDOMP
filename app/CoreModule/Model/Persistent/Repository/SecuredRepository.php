<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.11.19
 * Time: 15:12
 */

namespace App\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Interfaces\ISecuredRepository;
use Doctrine\ORM\QueryBuilder;
use Nette\Security\User;

/**
 * Class SecuredRepository
 * @package App\CoreModule\Model\Persistent\Repository
 */
abstract class SecuredRepository extends BaseRepository implements ISecuredRepository
{
    /**
     * @param User $user
     * @return QueryBuilder
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function getSecuredQueryBuilder(User $user): QueryBuilder
    {
        $qb = $this->createQueryBuilder('er');

        if (count($this->exclude)) {
            $qb->andwhere('er.id NOT IN (:exclude)')
            ->setParameter('exclude', $this->exclude);
        }

        if (!$user->isInRole('admin')) {
            $qb->andWhere('er.createdBy = :userId')
                ->setParameter('userId', $user->getId());
        }

        $qb->indexBy('er', 'er.id');

        return $qb;
    }

    /**
     * @param User $user
     * @return mixed
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function findAllowed(User $user)
    {
        return $this->getSecuredQueryBuilder($user)->getQuery()->getResult();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 22:03
 */

namespace App\CoreModule\Model\Persistent\Repository;


use Doctrine\ORM\QueryBuilder;
use Nette\Security\User;

/**
 * Class UserRepository
 * @package App\CoreModule\Model\Persistent\Repository
 */
class UserRepository extends SecuredRepository
{
    /**
     * @param User $user
     * @return QueryBuilder
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function getSecuredQueryBuilder(User $user): QueryBuilder
    {
        $qb = parent::getSecuredQueryBuilder($user);
        $qb->andWhere('er.isAdmin = FALSE');
        return $qb;
    }

    /**
     * @param string $login
     * @param array $rolesRequested
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findForAuthentication(string $login, array $rolesRequested = [])
    {
        $qb = $this->createQueryBuilder('er');

        $qb->where('er.email = :login')
            ->orWhere('er.username = :login')
            ->setParameter('login', $login);

        if (count($rolesRequested)) {
            $qb->andWhere('er.role IN (:rolesRequested)')
                ->setParameter('rolesRequested', $rolesRequested);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }
}
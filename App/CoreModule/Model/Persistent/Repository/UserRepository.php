<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 22:03
 */

namespace App\CoreModule\Model\Persistent\Repository;

/**
 * Class UserRepository
 * @package App\CoreModule\Model\Persistent\Repository
 */
class UserRepository extends SecuredRepository
{
    /**
     * @param string $login
     * @param array $rolesRequested
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findForAuthentication(string $login, array $rolesRequested = [])
    {
        bdump($login);
        bdump($rolesRequested);

        $qb = $this->createQueryBuilder('er');

        $qb->where('er.email = :login')
            ->orWhere('er.username = :login')
            ->setParameter('login', $login);

        if (count($rolesRequested)) {
            $qb->andWhere('er.role IN (:rolesRequested)')
                ->setParameter('rolesRequested', $rolesRequested);
        }

        bdump($qb->getQuery());

        return $qb->getQuery()->getOneOrNullResult();
    }
}
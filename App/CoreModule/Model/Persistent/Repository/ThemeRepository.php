<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 15:59
 */

namespace App\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\User;

/**
 * Class ThemeRepository
 * @package App\CoreModule\Model\Persistent\Repository
 */
class ThemeRepository extends SecuredRepository
{
    /**
     * @param User $teacher
     * @return mixed
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function findTeacherThemes(User $teacher)
    {
        $qb = $this->createQueryBuilder('er');

        $qb->andWhere('er.createdBy = :userId')
            ->setParameter('userId', $teacher->getId());
        $qb->indexBy('er', 'er.id');

        return $qb->getQuery()->getResult();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:49
 */

namespace App\Model\Repository;

use Doctrine\ORM\Id\SequenceGenerator;
use Kdyby\Doctrine\Entities\Attributes\Identifier;

/**
 * Class QuadraticEqTemplRepository
 * @package App\Model\Repository
 */
class QuadraticEqTemplRepository extends BaseRepository
{
    /**
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLastId(): int
    {
        $res = $this->createQueryBuilder("er")
            ->setMaxResults(1)
            ->select("er.id")
            ->orderBy("er.id", "DESC")
            ->getQuery()
            ->getSingleResult();

        return $res["id"];
    }
}
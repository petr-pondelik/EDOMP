<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 10:21
 */

namespace App\Model\Repository;

/**
 * Class ProblemTemplateRepository
 * @package App\Model\Repository
 */
class ProblemTemplateRepository extends BaseRepository
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
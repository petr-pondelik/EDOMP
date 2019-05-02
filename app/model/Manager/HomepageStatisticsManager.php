<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 2.5.19
 * Time: 14:03
 */

namespace App\Model\Manager;

use App\Helpers\ConstHelper;
use App\Model\Entity\Group;
use App\Model\Entity\SuperGroup;
use App\Model\Entity\User;
use Kdyby\Doctrine\EntityManager;

/**
 * Class HomepageStatisticsManager
 * @package App\Model\Manager
 */
class HomepageStatisticsManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * HomepageStatisticsManager constructor.
     * @param EntityManager $entityManager
     * @param ConstHelper $constHelper
     */
    public function __construct(EntityManager $entityManager, ConstHelper $constHelper)
    {
        $this->em = $entityManager;
        $this->constHelper = $constHelper;
    }

    /**
     * @param string $entityClass
     * @return int
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function getCnt(string $entityClass): int
    {
        $qb = $this->em->createQueryBuilder()
            ->select("e")
            ->from($entityClass, "e")
            ->indexBy("e", "e.id");

        if($entityClass === User::class)
            $qb = $qb->where("e.isAdmin = false");

        if($entityClass === Group::class || $entityClass === SuperGroup::class)
            $qb = $qb->where("e.id != :id")
                ->setParameter("id", $this->constHelper::ADMIN_GROUP);

        return count($qb->getQuery()->getResult());
    }
}
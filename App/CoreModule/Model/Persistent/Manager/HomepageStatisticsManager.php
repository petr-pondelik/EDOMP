<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 2.5.19
 * Time: 14:03
 */

namespace App\CoreModule\Model\Persistent\Manager;

use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Model\Persistent\Entity\Group;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal;
use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use App\CoreModule\Model\Persistent\Entity\SuperGroup;
use App\CoreModule\Model\Persistent\Entity\Theme;
use App\CoreModule\Model\Persistent\Entity\User;
use App\TeacherModule\ValueObject\HomepageStatistics;
use Kdyby\Doctrine\EntityManager;

/**
 * Class HomepageStatisticsManager
 * @package App\CoreModule\Model\Persistent\Manager
 */
class HomepageStatisticsManager
{
    /**
     * @var ConstraintEntityManager
     */
    protected $em;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * @var string
     */
    protected $where = '';

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
     * @param \Nette\Security\User $user
     * @return int
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function getCnt(string $entityClass, \Nette\Security\User $user): int
    {
        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from($entityClass, 'e')
            ->indexBy('e', 'e.id');

        if ($entityClass === User::class) {
            $qb = $qb->where('e.isAdmin = false');
        }

        if (!$user->isInRole('admin')) {
            $qb = $qb->where('e.createdBy = ' . $user->getId());
        }

        if ($entityClass === Group::class || $entityClass === SuperGroup::class) {
            $qb = $qb->where('e.id != :id')
                ->setParameter('id', $this->constHelper::ADMIN_GROUP);
        }

        return count($qb->getQuery()->getResult());
    }

    /**
     * @param \Nette\Security\User $user
     * @return array
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \App\TeacherModule\Exceptions\HomepageStatisticsException
     */
    public function getHomepageStatistics(\Nette\Security\User $user): HomepageStatistics
    {
        $connection = $this->em->getConnection();

        if (!$user->isInRole('admin')) {
            $this->where = 'WHERE created_by_id = ' . $user->getId();
        }

        $query = $connection->prepare("
            SELECT count(*) AS cnt FROM edomp.theme
            $this->where
            UNION ALL 
            SELECT count(*) FROM edomp.sub_theme
            $this->where
            UNION ALL 
            SELECT count(*) FROM edomp.test
            $this->where
            UNION ALL
            SELECT count(*) FROM edomp.super_group
            $this->where
            UNION ALL
            SELECT count(*) FROM edomp.group
            $this->where
            UNION ALL
            SELECT count(*) FROM edomp.user
            $this->where
            UNION ALL
            SELECT count(*) FROM edomp.logo
            $this->where
        ");

        try {
            $query->execute();
        } catch (\Exception $e) {
            bdump($e);
        }

        $problemTemplateRes = $this->getCnt(ProblemTemplate::class, $user);
        $problemFinalRes = $this->getCnt(ProblemFinal::class, $user);
        $res = $query->fetchAll();
        $res = array_merge($res, [
            'problemTemplateCnt' => $problemTemplateRes,
            'problemFinalCnt' => $problemFinalRes
        ]);

        return new HomepageStatistics($res);
    }

    /**
     * @param \Nette\Security\User $user
     * @return array
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function getThemesCnt(\Nette\Security\User $user): array
    {
        if ($user->isInRole('admin') || $user->isInRole('teacher')) {
            $res = $this->getCnt(Theme::class, $user);
        } else {
            $res = count($user->getIdentity()->themes);
        }
        return [
            'themesCnt' => $res
        ];
    }
}
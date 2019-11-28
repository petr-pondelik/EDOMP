<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 13:15
 */

namespace App\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Model\Persistent\Entity\Problem;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal;
use App\CoreModule\Model\Persistent\Traits\FilterTrait;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\QueryBuilder;

/**
 * Class ProblemFinalRepository
 * @package App\CoreModule\Model\Persistent\Repository
 */
class ProblemFinalRepository extends SecuredRepository
{
    use FilterTrait;

    /**
     * ProblemFinalRepository constructor.
     * @param $em
     * @param Mapping\ClassMetadata $class
     * @param ConstHelper $constHelper
     */
    public function __construct($em, Mapping\ClassMetadata $class, ConstHelper $constHelper)
    {
        parent::__construct($em, $class, $constHelper);
        $this->tableName = $this->getEntityManager()->getClassMetadata(Problem::class)->getTableName();
    }

    /**
     * @param int $themeId
     * @param array $filters
     * @return int
     */
    public function getStudentFilteredCnt(int $themeId, array $filters): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('pf')
            ->addSelect('sc')
            ->from(ProblemFinal::class, 'pf')
            ->innerJoin('pf.subTheme', 'sc')
            ->where('sc.theme = :themeId')
            ->andWhere('pf.studentVisible = true')
            ->setParameter('themeId', $themeId);

        $qb = $this->applyFilters($qb, $filters);

        return count($qb->getQuery()->getResult());

    }

    /**
     * @param int $themeId
     * @param int $limit
     * @param int $offset
     * @param array $filters
     * @return array
     */
    public function getStudentFiltered(int $themeId, int $limit, int $offset, array $filters): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('pf')
            ->addSelect('sc')
            ->from(ProblemFinal::class, 'pf')
            ->innerJoin('pf.subTheme', 'sc')
            ->where('sc.theme = :themeId')
            ->andWhere('pf.studentVisible = true')
            ->setParameter('themeId', $themeId);

        $qb = $this->applyFilters($qb, $filters);

        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param QueryBuilder $qb
     * @param array $filters
     * @return QueryBuilder
     */
    public function applyFilters(QueryBuilder $qb, array $filters): QueryBuilder
    {
        // Filter difficulty condition
        if(isset($filters['difficulty'])){
            $qb->andWhere('pf.difficulty IN (:difficultyIds)')
                ->setParameter('difficultyIds', $filters['difficulty']);
        }

        // Filter subtheme (theme) condition
        if(isset($filters['theme'])){
            $qb->andWhere('pf.subTheme IN (:subThemeIds)')
                ->setParameter('subThemeIds', $filters['theme']);
        }

        // Filter result condition
        if(isset($filters['result'])){
            if( in_array('0', $filters['result']) && !in_array('1', $filters['result']) ){
                $qb->andWhere('pf.result IS NOT NULL')
                    ->andWhere("pf.result <> ''");
            }
            else if( !in_array('0', $filters['result']) && in_array('1', $filters['result'])){
                $qb->andWhere("pf.result = ''");
            }
        }

        if(isset($filters['sort_by_difficulty'])){
            switch($filters['sort_by_difficulty']){
                case 0: $qb = $qb->orderBy('pf.id', 'ASC');
                    break;
                case 1: $qb = $qb->orderBy('pf.difficulty', 'ASC');
                    break;
                case 2: $qb = $qb->orderBy('pf.difficulty', 'DESC');
                    break;
            }
        }

        return $qb;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 13:15
 */

namespace App\CoreModule\Model\Persistent\Repository\ProblemFinal;

use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Model\Persistent\Entity\Problem;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
use App\CoreModule\Model\Persistent\Repository\SecuredRepository;
use App\CoreModule\Model\Persistent\Traits\FilterTrait;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\QueryBuilder;

/**
 * Class ProblemFinalRepository
 * @package App\CoreModule\Model\Persistent\Repository\ProblemFinal
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
     * @param int $categoryId
     * @param array $filters
     * @return int
     */
    public function getStudentFilteredCnt(int $categoryId, array $filters): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('pf')
            ->addSelect('sc')
            ->from(ProblemFinal::class, 'pf')
            ->innerJoin('pf.subCategory', 'sc')
            ->where('sc.category = :categoryId')
            ->andWhere('pf.studentVisible = true')
            ->setParameter('categoryId', $categoryId);

        $qb = $this->applyFilters($qb, $filters);

        return count($qb->getQuery()->getResult());

    }

    /**
     * @param int $categoryId
     * @param int $limit
     * @param int $offset
     * @param array $filters
     * @return array
     */
    public function getStudentFiltered(int $categoryId, int $limit, int $offset, array $filters): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('pf')
            ->addSelect('sc')
            ->from(ProblemFinal::class, 'pf')
            ->innerJoin('pf.subCategory', 'sc')
            ->where('sc.category = :categoryId')
            ->andWhere('pf.studentVisible = true')
            ->setParameter('categoryId', $categoryId);

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

        // Filter subcategory (theme) condition
        if(isset($filters['theme'])){
            $qb->andWhere('pf.subCategory IN (:subCategoryIds)')
                ->setParameter('subCategoryIds', $filters['theme']);
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
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 13:15
 */

namespace App\Model\Persistent\Repository\ProblemFinal;

use App\CoreModule\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
use App\Model\Persistent\Repository\BaseRepository;
use App\Model\Persistent\Traits\FilterTrait;
use Doctrine\ORM\QueryBuilder;

/**
 * Class ProblemFinalRepository
 * @package App\Model\Persistent\Repository
 */
class ProblemFinalRepository extends BaseRepository
{
    use FilterTrait;

    /**
     * @param int $categoryId
     * @param array $filters
     * @return int
     */
    public function getFilteredCnt(int $categoryId, array $filters): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('pf')
            ->addSelect('sc')
            ->from(ProblemFinal::class, 'pf')
            ->innerJoin('pf.subCategory', 'sc')
            ->where('sc.category = :categoryId')
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
    public function getFiltered(int $categoryId, int $limit, int $offset, array $filters): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('pf')
            ->addSelect('sc')
            ->from(ProblemFinal::class, 'pf')
            ->innerJoin('pf.subCategory', 'sc')
            ->where('sc.category = :categoryId')
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
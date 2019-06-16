<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 10:36
 */

namespace App\Model\Repository;

/**
 * Class ProblemTestAssociationRepository
 * @package App\Model\Repository
 */
class ProblemTestAssociationRepository extends BaseRepository
{
    /**
     * @param int $testId
     * @return array
     */
    public function findByTestInVariants(int $testId): array
    {
        $associations = $this->findBy(['test' => $testId]);
        $res = [];
        foreach ($associations as $association){
            $res[$association->getVariant()][] = $association;
        }
        return $res;
    }
}
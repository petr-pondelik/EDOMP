<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 10:36
 */

namespace App\CoreModule\Model\Persistent\Repository;

/**
 * Class ProblemTestAssociationRepository
 * @package App\CoreModule\Model\Persistent\Repository
 */
class ProblemFinalTestVariantAssociationRepository extends BaseRepository
{
    /**
     * @param int $testId
     * @return array
     */
    public function findByTestInVariants(int $testId): array
    {
        $associations = $this->findBy(['test' => $testId]);
        $res = [];
        foreach ($associations as $association) {
            $res[$association->getVariant()][] = $association;
        }
        return $res;
    }
}
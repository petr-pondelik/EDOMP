<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 14:10
 */

namespace App\Model\Persistent\Repository;

/**
 * Class ProblemConditionRepository
 * @package App\Model\Persistent\Repository
 */
class ProblemConditionRepository extends BaseRepository
{
    /**
     * @return array
     */
    public function findAssocByTypeAndAccessor(): array
    {
        $problemConditions = $this->findBy(['problemConditionType.isValidation' => false]);
        $res = [];
        foreach ($problemConditions as $problemCondition) {
            $res[$problemCondition->getProblemConditionType()->getId()][$problemCondition->getAccessor()] = $problemCondition;
        }
        return $res;
    }
}
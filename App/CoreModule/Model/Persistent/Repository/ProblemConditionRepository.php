<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 14:10
 */

namespace App\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemCondition;

/**
 * Class ProblemConditionRepository
 * @package App\CoreModule\Model\Persistent\Repository
 */
class ProblemConditionRepository extends BaseRepository
{
    /**
     * @return array
     */
    public function findAssocByTypeAndAccessor(): array
    {
        /** @var ProblemCondition[] $problemConditions */
        $problemConditions = $this->findBy(['problemConditionType.isValidation' => false]);
        $res = [];
        foreach ($problemConditions as $problemCondition) {
            $res[$problemCondition->getProblemConditionType()->getId()][$problemCondition->getAccessor()] = $problemCondition;
        }
        return $res;
    }
}
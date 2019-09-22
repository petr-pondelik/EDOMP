<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 15.9.19
 * Time: 18:28
 */

namespace App\Model\ProblemDuplicityModel;

use App\Model\Persistent\Entity\Problem;
use App\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;

/**
 * Class TemplateDuplicityState
 * @package App\Model\ProblemDuplicityModel
 */
class TemplateDuplicityState extends DuplicityState
{
    /**
     * @return bool
     */
    public function freeExists(): bool
    {
        return true;
    }

    /**
     * @param Problem $problem
     * @return bool
     */
    public function addUsed(Problem $problem): bool
    {
        $problemTemplateId = $problem->getProblemTemplate()->getId();
        $matchesIndex = $problem->getMatchesIndex();
        if( !isset($this->used[$problemTemplateId]) && !isset($this->used[$problemTemplateId][$matchesIndex]) ){
            $this->used[$problemTemplateId][$matchesIndex] = true;
            return true;
        }
        return false;
    }

    /**
     * @param ProblemTemplate $problemTemplate
     * @return array|null
     */
    public function getTemplateUsed(ProblemTemplate $problemTemplate): ?array
    {
        return $this->used[$problemTemplate->getId()] ?? null;
    }
}
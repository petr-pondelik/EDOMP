<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 11.9.19
 * Time: 19:36
 */

namespace App\TeacherModule\Interfaces;

use App\CoreModule\Model\Persistent\Entity\ProblemFinal;
use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;
use Nette\Utils\ArrayHash;

/**
 * Interface IProblemPlugin
 * @package App\TeacherModule\Interfaces
 */
interface IProblemPlugin
{
    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return ProblemTemplateNP
     */
    public function preprocess(ProblemTemplateNP $problemTemplate): ProblemTemplateNP;

    /**
     * @param $problemTemplate
     * @return int
     */
    public function validateBody(ProblemTemplateNP $problemTemplate): int;

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return bool
     */
    public function validateType(ProblemTemplateNP $problemTemplate): bool;

    /**
     * @param ProblemTemplate $problemTemplate
     * @param array|null $usedMatchesInx
     * @return ProblemFinal
     */
    public function createFinal(ProblemTemplate $problemTemplate, ?array $usedMatchesInx): ProblemFinal;

    /**
     * @param ProblemFinal $problem
     * @return ArrayHash
     */
    public function evaluate(ProblemFinal $problem): ArrayHash;
}
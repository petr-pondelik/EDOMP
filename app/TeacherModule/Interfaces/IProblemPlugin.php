<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 11.9.19
 * Time: 19:36
 */

namespace App\TeacherModule\Plugins;

use App\CoreModule\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;
use Nette\Utils\ArrayHash;

/**
 * Interface IProblemPlugin
 * @package App\TeacherModule\Plugins
 */
interface IProblemPlugin
{
    /**
     * @param $problemTemplate
     * @return int
     */
    public function validateBody(ProblemTemplateNP $problemTemplate): int;

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return ProblemTemplateNP
     */
    public function standardize(ProblemTemplateNP $problemTemplate): ProblemTemplateNP;

    /**
     * @param string $expression
     * @return string
     */
    public function standardizeFinal(string $expression): string;

    /**
     * @param ProblemFinal $problem
     * @return ArrayHash
     */
    public function evaluate(ProblemFinal $problem): ArrayHash;

    /**
     * @param ProblemTemplate $problemTemplate
     * @param array|null $usedMatchesInx
     * @return ArrayHash
     */
    public function constructProblemFinalData(ProblemTemplate $problemTemplate, ?array $usedMatchesInx): ArrayHash;

    /**
     * @param ProblemTemplate $problemTemplate
     * @param array|null $usedMatchesInx
     * @return ProblemFinal
     */
    public function constructProblemFinal(ProblemTemplate $problemTemplate, ?array $usedMatchesInx): ProblemFinal;
}
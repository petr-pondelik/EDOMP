<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 11.9.19
 * Time: 19:36
 */

namespace App\Plugins;

use App\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
use App\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use Nette\Utils\ArrayHash;

/**
 * Interface IProblemPlugin
 * @package App\Plugins
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
     * @return ArrayHash
     */
    public function constructProblemFinalData(ProblemTemplate $problemTemplate): ArrayHash;

    /**
     * @param ProblemTemplate $problemTemplate
     * @return ProblemFinal
     */
    public function constructProblemFinal(ProblemTemplate $problemTemplate): ProblemFinal;
}
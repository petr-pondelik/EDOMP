<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 15.9.19
 * Time: 17:55
 */

namespace App\TeacherModule\Services;

use App\TeacherModule\Exceptions\ProblemDuplicityException;
use App\TeacherModule\Model\NonPersistent\ProblemDuplicity\FinalDuplicityState;
use App\TeacherModule\Model\NonPersistent\ProblemDuplicity\TemplateDuplicityState;


/**
 * Class ProblemDuplicityModel
 * @package App\Model\ProblemDuplicityModel
 */
class ProblemDuplicity
{
    /**
     * @var FinalDuplicityState
     */
    protected $finalState;

    /**
     * @var TemplateDuplicityState
     */
    protected $templateState;

    /**
     * ProblemDuplicityModel constructor.
     */
    public function __construct()
    {
        $this->finalState = new FinalDuplicityState();
        $this->templateState = new TemplateDuplicityState();
    }

    /**
     * @param int $problemCnt
     * @param int $problemFinalCnt
     * @throws ProblemDuplicityException
     */
    public function checkFinalDuplicityState(int $problemCnt, int $problemFinalCnt): void
    {
        // If there isn't any free final problem and all the problems are finals, stop and throw exception
        if($problemFinalCnt >= $problemCnt && !$this->finalState->freeExists()){
            throw new ProblemDuplicityException('Test nelze vygenerovat bez opakujících se finálních úloh.');
        }
    }

    /**
     * @throws ProblemDuplicityException
     */
    public function checkDuplicityState(): void
    {
        if(!($this->finalState->freeExists() || $this->templateState->freeExists())){
            throw new ProblemDuplicityException('Test nelze vygenerovat bez opakujících se finálních úloh.');
        }
    }

    /**
     * @return FinalDuplicityState
     */
    public function getFinalState(): FinalDuplicityState
    {
        return $this->finalState;
    }

    /**
     * @param FinalDuplicityState $finalState
     */
    public function setFinalState(FinalDuplicityState $finalState): void
    {
        $this->finalState = $finalState;
    }

    /**
     * @return TemplateDuplicityState
     */
    public function getTemplateState(): TemplateDuplicityState
    {
        return $this->templateState;
    }

    /**
     * @param TemplateDuplicityState $templateState
     */
    public function setTemplateState(TemplateDuplicityState $templateState): void
    {
        $this->templateState = $templateState;
    }
}
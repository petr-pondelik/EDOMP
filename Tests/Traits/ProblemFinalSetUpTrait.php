<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.11.19
 * Time: 22:36
 */

namespace App\Tests\Traits;

use App\CoreModule\Model\Persistent\Entity\ProblemFinal\ProblemFinal;

/**
 * Trait ProblemFinalSetUpTrait
 * @package App\Tests\Traits
 */
trait ProblemFinalSetUpTrait
{
    /**
     * @var ProblemFinal
     */
    protected $problemFinal;

    public function setUpProblemFinal(): void
    {
        $entity = new ProblemFinal();
        $entity->setBody('TEST_BODY');
        $entity->setResult('TEST_RESULT');
        $entity->setMatchesIndex(1);
        $this->problemFinal = $entity;
    }
}
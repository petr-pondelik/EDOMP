<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.11.19
 * Time: 22:36
 */

namespace App\Tests\MockTraits\Entity;


use App\CoreModule\Model\Persistent\Entity\ProblemFinal;

/**
 * Trait ProblemFinalSetUpTrait
 * @package App\Tests\Traits
 */
trait ProblemFinalMockSetUpTrait
{
    /**
     * @var ProblemFinal
     */
    protected $problemFinalMock;

    public function setUpProblemFinalMock(): void
    {
        $this->problemFinalMock = $this->getMockBuilder(ProblemFinal::class)->disableOriginalConstructor()->getMock();
    }
}
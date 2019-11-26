<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.11.19
 * Time: 22:36
 */

namespace App\Tests\Traits;

use App\CoreModule\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait ProblemFinalSetUpTrait
 * @package App\Tests\Traits
 */
trait ProblemFinalMockSetUpTrait
{
    /**
     * @var MockObject
     */
    protected $problemFinalMock;

    public function setUpProblemFinalMock(): void
    {
        $this->problemFinalMock = $this->getMockBuilder(ProblemFinal::class)->disableOriginalConstructor()->getMock();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.11.19
 * Time: 18:18
 */

namespace App\Tests\Traits;

use App\CoreModule\Model\Persistent\Entity\ProblemCondition;

/**
 * Trait ProblemConditionSetUpTrait
 * @package App\Tests\Traits
 */
trait ProblemConditionSetUpTrait
{
    /**
     * @var ProblemCondition
     */
    protected $problemConditionMock;

    protected function setUpProblemConditionMock(): void
    {
        $this->problemConditionMock = $this->getMockBuilder(ProblemCondition::class)->disableOriginalConstructor()->getMock();
    }
}
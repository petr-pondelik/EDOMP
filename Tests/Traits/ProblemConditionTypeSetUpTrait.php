<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.11.19
 * Time: 0:24
 */

namespace App\Tests\Traits;

use App\CoreModule\Model\Persistent\Entity\ProblemConditionType;

/**
 * Trait ProblemConditionTypeSetUpTrait
 * @package App\Tests\Traits
 */
trait ProblemConditionTypeSetUpTrait
{
    /**
     * @var ProblemConditionType
     */
    protected $problemConditionTypeMock;

    public function setUpProblemConditionTypeMock(): void
    {
        $this->problemConditionTypeMock = $this->getMockBuilder(ProblemConditionType::class)->disableOriginalConstructor()->getMock();
    }
}
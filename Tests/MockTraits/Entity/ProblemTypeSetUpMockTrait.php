<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.11.19
 * Time: 17:59
 */

namespace App\Tests\MockTraits\Entity;

use App\CoreModule\Model\Persistent\Entity\ProblemType;

/**
 * Trait ProblemTypeSetUpMockTrait
 * @package App\Tests\Traits
 */
trait ProblemTypeSetUpMockTrait
{
    /**
     * @var ProblemType
     */
    protected $problemTypeMock;

    public function setUpProblemTypeMock(): void
    {
        $this->problemTypeMock = $this->getMockBuilder(ProblemType::class)->disableOriginalConstructor()->getMock();
    }
}
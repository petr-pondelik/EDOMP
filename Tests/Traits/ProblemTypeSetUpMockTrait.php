<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.11.19
 * Time: 17:59
 */

namespace App\Tests\Traits;

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

    public function setUpTestMock(): void
    {
        $this->problemTypeMock = $this->getMockBuilder(ProblemType::class)->disableOriginalConstructor()->getMock();
    }
}
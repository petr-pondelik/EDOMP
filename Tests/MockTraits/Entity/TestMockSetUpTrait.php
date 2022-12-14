<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.11.19
 * Time: 17:50
 */

namespace App\Tests\MockTraits\Entity;

use App\CoreModule\Model\Persistent\Entity\Test;

/**
 * Trait TestMockSetUpTrait
 * @package App\Tests\Traits
 */
trait TestMockSetUpTrait
{
    /**
     * @var Test
     */
    protected $testMock;

    public function setUpTestMock(): void
    {
        $this->testMock = $this->getMockBuilder(Test::class)->disableOriginalConstructor()->getMock();
    }
}
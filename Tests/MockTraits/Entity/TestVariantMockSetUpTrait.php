<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.11.19
 * Time: 16:05
 */

namespace App\Tests\MockTraits\Entity;

use App\CoreModule\Model\Persistent\Entity\TestVariant;

/**
 * Trait TestVariantMockSetUpTrait
 * @package App\Tests\Traits
 */
trait TestVariantMockSetUpTrait
{
    /**
     * @var TestVariant
     */
    protected $testVariantMock;

    protected function setUpTestVariantMock(): void
    {
        $this->testVariantMock = $this->getMockBuilder(TestVariant::class)->disableOriginalConstructor()->getMock();
    }
}
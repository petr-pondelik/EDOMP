<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.11.19
 * Time: 22:57
 */

namespace App\Tests\MockTraits\Entity;

use App\CoreModule\Model\Persistent\Entity\SuperGroup;

/**
 * Trait SuperGroupMockSetUpTrait
 * @package App\Tests\Traits
 */
trait SuperGroupMockSetUpTrait
{
    /**
     * @var SuperGroup
     */
    protected $superGroupMock;

    public function setUpSuperGroupMock(): void
    {
        $this->superGroupMock = $this->getMockBuilder(SuperGroup::class)->disableOriginalConstructor()->getMock();
    }
}
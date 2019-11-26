<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.11.19
 * Time: 22:57
 */

namespace App\Tests\Traits;

use App\CoreModule\Model\Persistent\Entity\Group;

/**
 * Trait GroupSetUpTrait
 * @package App\Tests\Traits
 */
trait GroupMockSetUpTrait
{
    /**
     * @var Group
     */
    protected $groupMock;

    public function setUpGroupMock(): void
    {
        $this->groupMock = $this->getMockBuilder(Group::class)->disableOriginalConstructor()->getMock();
    }
}
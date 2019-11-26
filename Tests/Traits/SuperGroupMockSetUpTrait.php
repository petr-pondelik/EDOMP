<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.11.19
 * Time: 22:57
 */

namespace App\Tests\Traits;

use App\CoreModule\Model\Persistent\Entity\SuperGroup;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait SuperGroupMockSetUpTrait
 * @package App\Tests\Traits
 */
trait SuperGroupMockSetUpTrait
{
    /**
     * @var MockObject
     */
    protected $superGroupMock;

    public function setUpSuperGroupMock(): void
    {
        $this->superGroupMock = $this->getMockBuilder(SuperGroup::class)->disableOriginalConstructor()->getMock();
    }
}
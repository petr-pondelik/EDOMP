<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.11.19
 * Time: 22:56
 */

namespace App\Tests\Traits;

use App\CoreModule\Model\Persistent\Entity\User;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait UserSetUpTrait
 * @package App\Tests\Traits
 */
trait UserMockSetUpTrait
{
    /**
     * @var MockObject
     */
    protected $userMock;

    public function setUpUserMock(): void
    {
        $this->userMock = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();
    }
}
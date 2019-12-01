<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.11.19
 * Time: 17:38
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\User;
use App\CoreModule\Model\Persistent\Repository\UserRepository;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait UserRepositoryMockTrait
 * @package App\Tests\MockTraits\Repository
 */
trait UserRepositoryMockTrait
{
    /**
     * @var MockObject
     */
    protected $userRepositoryMock;

    /**
     * @var User
     */
    protected $firstUser;

    /**
     * @var User
     */
    protected $secondUser;

    protected function setUpUserRepositoryMock(): void
    {
        $this->userRepositoryMock = $this->getMockBuilder(UserRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first User
        $firstUser = new User();
        $firstUser->setId(1);
        $firstUser->setUsername('TEST_USERNAME_FIRST');
        $firstUser->setPassword('TEST_PASSWORD_FIRST');
        $firstUser->setEmail('TEST_EMAIL_FIRST');
        $firstUser->setUsername('TEST_USERNAME_FIRST');
        $firstUser->setFirstName('TEST_FIRSTNAME_FIRST');
        $firstUser->setLastName('TEST_LASTNAME_FIRST');
        $this->firstUser = $firstUser;

        // Create second User
        $secondUser = new User();
        $secondUser->setId(2);
        $secondUser->setUsername('TEST_USERNAME_SECOND');
        $secondUser->setPassword('TEST_PASSWORD_SECOND');
        $firstUser->setEmail('TEST_EMAIL_SECOND');
        $firstUser->setUsername('TEST_USERNAME_SECOND');
        $firstUser->setFirstName('TEST_FIRSTNAME_SECOND');
        $firstUser->setLastName('TEST_LASTNAME_SECOND');
        $this->secondUser = $secondUser;

        // Set UserRepository expected return values for find
        $this->userRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($firstUser, $secondUser) {
                switch ($arg) {
                    case 1: return $firstUser;
                    case 2: return $secondUser;
                    default: return null;
                }
            });
    }
}
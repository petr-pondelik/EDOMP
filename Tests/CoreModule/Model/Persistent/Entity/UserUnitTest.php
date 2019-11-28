<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.11.19
 * Time: 18:08
 */

namespace App\Tests\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Entity\User;
use App\Tests\Traits\GroupMockSetUpTrait;
use App\Tests\Traits\RoleMockSetUpTrait;
use App\Tests\Traits\UserMockSetUpTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Security\Passwords;

/**
 * Class UserUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
final class UserUnitTest extends PersistentEntityTestCase
{
    use UserMockSetUpTrait;
    use RoleMockSetUpTrait;
    use GroupMockSetUpTrait;

    /**
     * @var array
     */
    protected $errorMessages = [
        "Username can't be blank.",
        "Email can't be blank.",
        "Password can't be blank.",
        "FirstName can't be blank.",
        "LastName can't be blank.",
        "Role can't be blank."
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpUserMock();
        $this->setUpRoleMock();
        $this->setUpGroupMock();
    }

    public function testValidState(): void
    {
        $entity = new User();
        $password = 'TEST_PASSWORD';
        $userName = 'TEST_USER_NAME';
        $email = 'TEST_EMAIL';
        $firstName = 'TEST_FIRST_NAME';
        $lastName = 'TEST_LAST_NAME';
        $groups = new ArrayCollection([$this->groupMock]);

        $this->assertInstanceOf(User::class, $entity);
        $this->assertTrue($entity->isTeacherLevelSecured());
        $this->assertFalse($entity->isAdmin());
        $this->assertCount(0, $entity->getGroups()->getValues());
        $this->assertNull($entity->getCreatedBy());

        $entity->setPassword($password);
        $entity->setUsername($userName);
        $entity->setEmail($email);
        $entity->setFirstName($firstName);
        $entity->setLastName($lastName);
        $entity->setGroups($groups);
        $entity->setRole($this->roleMock);

        $this->assertEquals($userName, (string) $entity);
        $this->assertTrue(Passwords::verify($password, $entity->getPassword()));
        $this->assertEquals($userName, $entity->getUsername());
        $this->assertEquals($email, $entity->getEmail());
        $this->assertEquals($firstName, $entity->getFirstName());
        $this->assertEquals($lastName, $entity->getLastName());
        $this->assertEquals($groups, $entity->getGroups());
        $this->assertEquals($this->roleMock, $entity->getRole());

        $this->assertValidByValidator($entity);
    }

    public function testInvalidState(): void
    {
        $entity = new User();
        $this->assertValidatorViolations($entity);
    }
}
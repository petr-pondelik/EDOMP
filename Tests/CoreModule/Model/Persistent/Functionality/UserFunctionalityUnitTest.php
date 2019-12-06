<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.12.19
 * Time: 0:01
 */

namespace App\Tests\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\User;
use App\CoreModule\Model\Persistent\Functionality\UserFunctionality;
use App\Tests\MockTraits\Repository\GroupRepositoryMockTrait;
use App\Tests\MockTraits\Repository\RoleRepositoryMockTrait;
use App\Tests\MockTraits\Repository\UserRepositoryMockTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\DateTime;

/**
 * Class UserFunctionalityUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Functionality
 */
class UserFunctionalityUnitTest extends FunctionalityUnitTestCase
{
    use UserRepositoryMockTrait;
    use RoleRepositoryMockTrait;
    use GroupRepositoryMockTrait;

    /**
     * @var UserFunctionality
     */
    protected $functionality;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpUserRepositoryMock();
        $this->setUpRoleRepositoryMock();
        $this->setUpGroupRepositoryMock();
        $this->functionality = new UserFunctionality($this->em, $this->userRepositoryMock, $this->roleRepositoryMock, $this->groupRepositoryMock);
    }

    public function testCreate(): void
    {
        // Data for User create
        $data = [
            'email' => 'TEST_EMAIL',
            'username' => 'TEST_USERNAME',
            'password' => 'TEST_PASSWORD',
            'firstName' => 'TEST_FIRSTNAME',
            'lastName' => 'TEST_LASTNAME',
            'role' => 1,
            'groups' => [1, 2],
            'created' => $this->dateTimeStr
        ];

        // Prepare expected User entity
        $expected = new User();
        $expected->setId(3);
        $expected->setEmail($data['email']);
        $expected->setUsername($data['username']);
        $expected->setPassword($data['password']);
        $expected->setFirstName($data['firstName']);
        $expected->setLastName($data['lastName']);
        $expected->setRole($this->firstRole);
        $expected->setGroups(new ArrayCollection([$this->firstGroup, $this->secondGroup]));
        $expected->setCreated(DateTime::from($data['created']));

        // Create User entity and test it against expected
        /** @var User $created */
        $created = $this->functionality->create($data);
        $created->setId(3);
        $created->setPassword($expected->getPassword(), false);
        $this->assertEquals($expected, $created);

        // Data for User create
        $data = [
            'email' => 'TEST_EMAIL',
            'password' => 'TEST_PASSWORD',
            'firstName' => 'TEST_FIRSTNAME',
            'lastName' => 'TEST_LASTNAME',
            'role' => 1,
            'groups' => [1, 2],
            'created' => $this->dateTimeStr
        ];

        $expected->setUsername($data['email']);

        // Create User entity and test it against expected
        /** @var User $created */
        $created = $this->functionality->create($data);
        $created->setId(3);
        $created->setPassword($expected->getPassword(), false);
        $this->assertEquals($expected, $created);
    }

    public function testCreateRoleNotFound(): void
    {
        // Data for User create
        $data = [
            'email' => 'TEST_EMAIL',
            'password' => 'TEST_PASSWORD',
            'firstName' => 'TEST_FIRSTNAME',
            'lastName' => 'TEST_LASTNAME',
            'role' => 50,
            'groups' => [1, 2],
            'created' => $this->dateTimeStr
        ];

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Role not found.');
        $this->functionality->create($data);
    }

    public function testCreateUserNotFound(): void
    {
        // Data for User create
        $data = [
            'email' => 'TEST_EMAIL',
            'password' => 'TEST_PASSWORD',
            'firstName' => 'TEST_FIRSTNAME',
            'lastName' => 'TEST_LASTNAME',
            'role' => 1,
            'groups' => [50, 2],
            'created' => $this->dateTimeStr
        ];

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Group not found.');
        $this->functionality->create($data);
    }

    public function testUpdate(): void
    {
        // Data for User update
        $data = [
            'username' => 'TEST_USERNAME_UPDATE',
            'email' => 'TEST_EMAIL_UPDATE',
            'password' => 'TEST_PASSWORD_UPDATE',
            'firstName' => 'TEST_FIRSTNAME_UPDATE',
            'lastName' => 'TEST_LASTNAME_UPDATE',
            'role' => 1,
            'groups' => [1, 2]
        ];

        // Prepare expected object
        $expected = $this->firstUser;
        $expected->setUsername($data['username']);
        $expected->setEmail($data['email']);
        $expected->setPassword($data['password']);
        $expected->setFirstName($data['firstName']);
        $expected->setLastName($data['lastName']);
        $expected->setRole($this->firstRole);
        $expected->setGroups(new ArrayCollection([$this->firstGroup, $this->secondGroup]));

        // Update User and test it against expected
        $updated = $this->functionality->update(1, $data);
        $this->assertEquals($expected, $updated);

        // Data for User update
        $data = [
            'email' => 'TEST_EMAIL_UPDATE',
            'password' => 'TEST_PASSWORD_UPDATE',
            'firstName' => 'TEST_FIRSTNAME_UPDATE',
            'lastName' => 'TEST_LASTNAME_UPDATE',
            'role' => 1,
            'groups' => [1, 2]
        ];

        // Prepare expected object
        $expected = $this->firstUser;
        $expected->setEmail($data['email']);
        $expected->setPassword($data['password']);
        $expected->setFirstName($data['firstName']);
        $expected->setLastName($data['lastName']);
        $expected->setRole($this->firstRole);
        $expected->setGroups(new ArrayCollection([$this->firstGroup, $this->secondGroup]));

        // Update User and test it against expected
        $updated = $this->functionality->update(1, $data);
        $this->assertEquals($expected, $updated);

        // Test invalid update
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Entity for update not found.');
        $this->functionality->update(50, $data);
    }

    /**
     * @throws EntityNotFoundException
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function testUpdatePassword(): void
    {
        $password = '123456789';

        // Prepare expected object
        $expected = $this->firstUser;
        $expected->setPassword($password);

        // Update user and test it against expected
        $updated = $this->functionality->updatePassword(1, '123456789');
        $this->assertEquals($expected, $updated);

        // Test invalid update
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Entity for update not found.');
        $this->functionality->updatePassword(50, '123456789');
    }

    /**
     * @throws EntityNotFoundException
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function testUpdatePasswordByEmail(): void
    {
        $email = 'TEST_EMAIL_FIRST';
        $password = '123456789';

        // Prepare expected object
        $expected = $this->firstUser;
        $expected->setPassword($password);

        // Update user and test it against expected
        $updated = $this->functionality->updatePasswordByEmail($email, $password);
        $this->assertEquals($expected, $updated);

        // Test invalid update
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Entity for update not found.');
        $this->functionality->updatePasswordByEmail('adfadf', $password);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function testDelete(): void
    {
        $this->assertTrue($this->functionality->delete(1));
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Entity for deletion was not found.');
        $this->functionality->delete(50);
    }
}
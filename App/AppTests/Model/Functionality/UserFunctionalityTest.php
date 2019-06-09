<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 9.6.19
 * Time: 12:11
 */

namespace App\AppTests\Model\Functionality;

use App\Model\Entity\Group;
use App\Model\Entity\Role;
use App\Model\Entity\SuperGroup;
use App\Model\Entity\User;
use App\Model\Functionality\UserFunctionality;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\RoleRepository;
use App\Model\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class UserFunctionalityTest
 * @package App\AppTests\Model\Functionality
 */
class UserFunctionalityTest extends FunctionalityTestCase
{
    /**
     * @var MockObject
     */
    protected $roleRepositoryMock;

    /**
     * @var MockObject
     */
    protected $groupRepositoryMock;

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        // Create default SuperGroup
        $superGroup = new SuperGroup();
        $superGroup->setId(1);
        $superGroup->setLabel('TEST_SUPER_GROUP_DEFAULT');

        // Create first Group
        $firstGroup = new Group();
        $firstGroup->setId(1);
        $firstGroup->setLabel('TEST_FIRST_GROUP');
        $firstGroup->setSuperGroup($superGroup);

        // Create second Group
        $secondGroup = new Group();
        $secondGroup->setId(2);
        $secondGroup->setLabel('TEST_SECOND_GROUP');
        $secondGroup->setSuperGroup($superGroup);

        // Create first Role
        $firstRole = new Role();
        $firstRole->setId(1);
        $firstRole->setLabel('TEST_FIRST_ROLE');
        $firstRole->setKey('test_first_role');

        // Create second Role
        $secondRole = new Role();
        $secondRole->setId(2);
        $secondRole->setLabel('TEST_SECOND_ROLE');
        $secondRole->setKey('test_second_role');

        // Mock the UserRepository
        $this->repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Mock the RoleRepository
        $this->roleRepositoryMock = $this->getMockBuilder(RoleRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for RoleRepository find method
        $this->roleRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use (
                $firstRole, $secondRole
            ) {
                switch ($arg){
                    case 1: return $firstRole;
                    case 2: return $secondRole;
                    default: return null;
                }
            });

        // Mock the GroupRepository
        $this->groupRepositoryMock = $this->getMockBuilder(GroupRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for GroupRepository find method
        $this->groupRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use (
                $firstGroup, $secondGroup
            ) {
                switch ($arg){
                    case 1: return $firstGroup;
                    case 2: return $secondGroup;
                    default: return null;
                }
            });

        //Instantiate tested class
        $this->functionality = new UserFunctionality($this->em, $this->repositoryMock, $this->roleRepositoryMock, $this->groupRepositoryMock);
    }

    /**
     * @throws \Exception
     */
    public function testFunctionality(): void
    {
        // Data for User create
        $data = ArrayHash::from([
            'username' => 'TEST_USERNAME',
            'password' => 'TEST_PASSWORD',
            'role' => 1,
        ]);
        $data->groups = [1];

        // Prepare User expected object
        $userExpected = new User();
        $userExpected->setUsername($data->username);
        $userExpected->setPassword(Passwords::hash($data->password));
        $userExpected->setRole($this->roleRepositoryMock->find($data->role));
        $userExpected->addGroup($this->groupRepositoryMock->find($data->groups[0]));

        // Create User
        $user = $this->functionality->create($data);

        // Test created User against expected object
        $this->assertEquals($userExpected->getUsername(), $user->getUsername());
        $this->assertTrue(Passwords::verify($data->password, $user->getPassword()));
        $this->assertEquals($userExpected->getRole(), $user->getRole());
        $this->assertEquals($userExpected->getGroups(), $user->getGroups());
        $this->assertEquals($userExpected->getGroupsId(), $user->getGroupsId());

        // Set expected return values for UserRepository find method
        $this->repositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use (
                $user
            ) {
                switch ($arg){
                    case 1: return $user;
                    default: return null;
                }
            });

        // Data for User update
        $data = ArrayHash::from([
            'username' => 'TEST_USERNAME_NEW',
            'password' => 'TEST_PASSWORD_NEW',
            'change_password' => true,
            'role' => 2,
        ]);
        $data->groups = [1, 2];

        // Prepare User expected object
        $userExpected->setUsername($data->username);
        $userExpected->setPassword($data->password);
        $userExpected->setRole($this->roleRepositoryMock->find($data->role));
        $userExpected->setGroups(new ArrayCollection());
        $userExpected->addGroup($this->groupRepositoryMock->find($data->groups[0]));
        $userExpected->addGroup($this->groupRepositoryMock->find($data->groups[1]));

        // Update User
        $user = $this->functionality->update(1, $data);

        // Test updated User against expected object
        $this->assertEquals($userExpected->getUsername(), $user->getUsername());
        $this->assertTrue(Passwords::verify($data->password, $user->getPassword()));
        $this->assertEquals($userExpected->getRole(), $user->getRole());
        $this->assertEquals($userExpected->getGroups(), $user->getGroups());
        $this->assertEquals($userExpected->getGroupsId(), $user->getGroupsId());
    }
}
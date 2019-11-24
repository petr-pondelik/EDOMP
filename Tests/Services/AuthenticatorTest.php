<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 10.6.19
 * Time: 11:43
 */

namespace Tests\Model\Services;


use App\Model\Entity\Category;
use App\Model\Entity\Group;
use App\Model\Entity\Role;
use App\Model\Entity\SuperGroup;
use App\Model\Entity\User;
use App\Model\Repository\CategoryRepository;
use App\Model\Repository\UserRepository;
use App\Services\Authenticator;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Security\AuthenticationException;
use Nette\Security\Identity;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class AuthenticatorTest
 * @package App\AppTests\Services
 */
class AuthenticatorTest extends TestCase
{
    /**
     * @var Authenticator
     */
    protected $authenticator;

    /**
     * @var MockObject
     */
    protected $userRepositoryMock;

    /**
     * @var MockObject
     */
    protected $categoryRepositoryMock;

    /**
     * @var Category
     */
    protected $firstCategory;

    /**
     * @var Category
     */
    protected $secondCategory;

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        // Create first Category
        $firstCategory = new Category();
        $firstCategory->setLabel('TEST_CATEGORY_FIRST');
        $firstCategory->setId(1);
        $this->firstCategory = $firstCategory;

        // Create second Category
        $secondCategory = new Category();
        $secondCategory->setLabel('TEST_CATEGORY_SECOND');
        $secondCategory->setId(2);
        $this->secondCategory = $secondCategory;

        // Create SuperGroup
        $superGroup = new SuperGroup();
        $superGroup->setLabel('TEST_SUPER_GROUP_DEFAULT');
        $superGroup->setId(1);

        // Create Group
        $group = new Group();
        $group->setLabel('TEST_GROUP');
        $group->setSuperGroup($superGroup);
        $group->setCategories(new ArrayCollection([$firstCategory]));
        $group->setId(1);

        // Create Admin Role
        $adminRole = new Role();
        $adminRole->setLabel('Administrátor');
        $adminRole->setKey('admin');
        $adminRole->setId(1);

        // Create Student Role
        $studentRole = new Role();
        $studentRole->setLabel('Student');
        $studentRole->setKey('student');
        $studentRole->setId(2);

        // Create first User
        $firstUser = new User();
        $firstUser->setUsername('TEST_USERNAME_FIRST');
        $firstUser->setPassword('TEST_PASSWORD_FIRST');
        $firstUser->setFirstName('TEST_FIRST_NAME_FIRST');
        $firstUser->setLastName('TEST_LAST_NAME_FIRST');
        $firstUser->setGroups(new ArrayCollection([$group]));
        $firstUser->setRole($adminRole);
        $firstUser->setId(1);

        // Create second User
        $secondUser = new User();
        $secondUser->setUsername('TEST_USERNAME_SECOND');
        $secondUser->setPassword('TEST_PASSWORD_SECOND');
        $secondUser->setFirstName('TEST_FIRST_NAME_SECOND');
        $secondUser->setLastName('TEST_LAST_NAME_SECOND');
        $secondUser->setGroups(new ArrayCollection([$group]));
        $secondUser->setRole($studentRole);
        $secondUser->setId(2);

        // Mock the UserRepository
        $this->userRepositoryMock = $this->getMockBuilder(UserRepository::class)
            ->setMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for UserRepository findOneBy method
        $this->userRepositoryMock->expects($this->any())
            ->method('findOneBy')
            ->willReturnCallback(static function ($arg) use (
                $firstUser, $secondUser
            ) {
                switch ($arg){
                    case ['username' => 'TEST_USERNAME_FIRST']:
                        return $firstUser;
                    case ['username' => 'TEST_USERNAME_SECOND']:
                        return $secondUser;
                    default: return null;
                }
            });

        // Mock the CategoryRepository
        $this->categoryRepositoryMock = $this->getMockBuilder(CategoryRepository::class)
            ->setMethods(['findPairs', 'find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for CategoryRepository find method
        $this->categoryRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use (
                $firstCategory, $secondCategory
            ) {
                switch ($arg){
                    case 1:
                        return $firstCategory;
                    case 2:
                        return $secondCategory;
                    default: return null;
                }
            });

        // Set expected return values for CategoryRepository findPairs method
        $this->categoryRepositoryMock->expects($this->any())
            ->method('findPairs')
            ->willReturn([
                $firstCategory, $secondCategory
            ]);

        // Instantiate tested class
        $this->authenticator = new Authenticator($this->userRepositoryMock, $this->categoryRepositoryMock);
    }

    /**
     * @throws \Nette\Security\AuthenticationException
     */
    public function testAuthenticate(): void
    {
        // Prepare expected Identity object for admin User
        $identityExpected = new Identity(1, 'admin', [
            'username' => 'TEST_USERNAME_FIRST',
            'categories' => [
                $this->firstCategory, $this->secondCategory
            ],
            'roleLabel' => 'Administrátor',
            'firstName' => 'TEST_FIRST_NAME_FIRST',
            'lastName' => 'TEST_LAST_NAME_FIRST'
        ]);

        // Authenticate admin User and get it's identity
        $identity = $this->authenticator->authenticate([
            'TEST_USERNAME_FIRST',
            'TEST_PASSWORD_FIRST'
        ]);

        // Test created Identity against expected Identity
        $this->assertEquals($identityExpected, $identity);

        // Prepare expected Identity object for student User
        $identityExpected = new Identity(2, 'student', [
            'username' => 'TEST_USERNAME_SECOND',
            'categories' => [
                1 => $this->firstCategory
            ],
            'roleLabel' => 'Student',
            'firstName' => 'TEST_FIRST_NAME_SECOND',
            'lastName' => 'TEST_LAST_NAME_SECOND'
        ]);

        // Authenticate student User and get it's Identity
        $identity = $this->authenticator->authenticate([
            'TEST_USERNAME_SECOND',
            'TEST_PASSWORD_SECOND'
        ]);

        // Test created Identity against expected Identity
        $this->assertEquals($identityExpected, $identity);

        $this->expectException(AuthenticationException::class);

        // Authenticate with invalid credentials
        $this->authenticator->authenticate([
            'TEST_USERNAME_FIRST',
            'TEST_PASSWORD_FIRSTTTT'
        ]);
    }
}
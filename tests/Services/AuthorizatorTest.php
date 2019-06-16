<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 10.6.19
 * Time: 15:59
 */

namespace Tests\Model\Services;

use App\Model\Entity\Category;
use App\Model\Entity\Group;
use App\Model\Entity\Role;
use App\Model\Entity\SuperGroup;
use App\Model\Entity\User;
use App\Services\Authorizator;
use Nette\Security\Identity;
use PHPUnit\Framework\TestCase;

/**
 * Class AuthorizatorTest
 * @package App\AppTests\Services
 */
class AuthorizatorTest extends TestCase
{
    /**
     * @var Authorizator
     */
    protected $authorizator;

    /**
     * @var Category
     */
    protected $firstCategory;

    /**
     * @var Category
     */
    protected $secondCategory;

    /**
     * @var User
     */
    protected $identityUser;

    /**
     * @var User
     */
    protected $firstUser;

    /**
     * @var User
     */
    protected $secondUser;

    /**
     * @var Group
     */
    protected $firstGroup;

    /**
     * @var Group
     */
    protected $secondGroup;

    /**
     * @var SuperGroup
     */
    protected $firstSuperGroup;

    /**
     * @var SuperGroup
     */
    protected $secondSuperGroup;

    /**
     * @var Identity
     */
    protected $identity;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        // Create first SuperGroup
        $firstSuperGroup = new SuperGroup();
        $firstSuperGroup->setLabel('TEST_SUPER_GROUP_FIRST');
        $firstSuperGroup->setId(1);

        // Create second SuperGroup
        $secondSuperGroup = new SuperGroup();
        $secondSuperGroup->setLabel('TEST_SUPER_GROUP_SECOND');
        $secondSuperGroup->setId(2);

        // Create first Group
        $firstGroup = new Group();
        $firstGroup->setLabel('TEST_GROUP_FIRST');
        $firstGroup->setSuperGroup($firstSuperGroup);
        $firstGroup->setId(1);

        // Create second Group
        $secondGroup = new Group();
        $secondGroup->setLabel('TEST_GROUP_SECOND');
        $secondGroup->setSuperGroup($secondSuperGroup);
        $secondGroup->setId(2);

        // Create first Category
        $firstCategory = new Category();
        $firstCategory->setLabel('TEST_CATEGORY_FIRST');
        $firstCategory->setId(1);

        // Create second Category
        $secondCategory = new Category();
        $secondCategory->setLabel('TEST_CATEGORY_SECOND');
        $secondCategory->setId(2);

        // Create Role
        $role = new Role();
        $role->setLabel('TEST_ROLE');
        $role->setKey('test_role');

        // Create first User
        $firstUser = new User();
        $firstUser->setUsername('TEST_USERNAME_FIRST');
        $firstUser->setPassword('TEST_PASSWORD_FIRST');
        $firstUser->setRole($role);
        $firstUser->addGroup($firstGroup);
        $firstUser->setId(1);

        // Create second User
        $secondUser = new User();
        $secondUser->setUsername('TEST_USERNAME_SECOND');
        $secondUser->setPassword('TEST_PASSWORD_SECOND');
        $secondUser->setRole($role);
        $secondUser->addGroup($firstGroup);
        $secondUser->setId(2);

        // Create User that belongs to the identity
        $identityUser = new User();
        $identityUser->setUsername('TEST_USERNAME_IDENTITY');
        $identityUser->setPassword('TEST_PASSWORD_IDENTITY');
        $identityUser->setRole($role);
        $identityUser->addGroup($firstGroup);
        $identityUser->setId(3);

        // Complete created entities
        $firstSuperGroup->setCreatedBy($identityUser);
        $firstGroup->setCreatedBy($identityUser);
        $firstUser->setCreatedBy($identityUser);

        // Create Identity object
        $identity = new Identity(3, 'test_role', [
            'username' => 'TEST_USERNAME_IDENTITY',
            'categories' => [
                1 => $firstCategory
            ],
            'roleLabel' => 'TEST_ROLE'
        ]);

        // Set entities to the TestCase class
        $this->firstUser = $firstUser;
        $this->secondUser = $secondUser;
        $this->identityUser = $identityUser;
        $this->firstSuperGroup = $firstSuperGroup;
        $this->secondSuperGroup = $secondSuperGroup;
        $this->firstGroup = $firstGroup;
        $this->secondGroup = $secondGroup;
        $this->firstCategory = $firstCategory;
        $this->secondCategory = $secondCategory;
        $this->identity = $identity;

        // Instantiate tested class
        $this->authorizator = new Authorizator();
    }

    public function testIsCategoryAllowed(): void
    {
        $this->assertTrue($this->authorizator->isCategoryAllowed($this->identity, 1));
        $this->assertFalse($this->authorizator->isCategoryAllowed($this->identity, 2));
    }

    public function testIsUserAllowed(): void
    {
        $this->assertTrue($this->authorizator->isUserAllowed($this->identity, $this->firstUser));
        $this->assertFalse($this->authorizator->isUserAllowed($this->identity, $this->secondUser));
    }

    public function testIsGroupAllowed(): void
    {
        $this->assertTrue($this->authorizator->isGroupAllowed($this->identity, $this->firstGroup));
        $this->assertFalse($this->authorizator->isGroupAllowed($this->identity, $this->secondGroup));
    }

    public function setIsSuperGroupAllowed(): void
    {
        $this->assertTrue($this->authorizator->isSuperGroupAllowed($this->identity, $this->firstSuperGroup));
        $this->assertFalse($this->authorizator->isSuperGroupAllowed($this->identity, $this->secondSuperGroup));
    }
}
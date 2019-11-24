<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.6.19
 * Time: 22:29
 */

namespace Tests\Model\Entity;


use App\Model\Entity\Role;
use App\Model\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Security\Passwords;

/**
 * Class UserTest
 * @package AppTests\Model\Entity
 */
class UserTest extends EntityTestCase
{
    /**
     * @var Role
     */
    protected $role;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $role = new Role();
        $role->setLabel('TEST_ROLE');
        $role->setKey('test_role');
        $this->role = $role;
    }

    /**
     * @throws \Exception
     */
    public function testValues(): void
    {
        $entity = new User();
        $entity->setUsername('TEST_USER_NAME');
        $entity->setPassword('TEST_USER_PASSWORD');
        $entity->setRole($this->role);

        $this->assertEquals($entity->getUsername(), 'TEST_USER_NAME');
        $this->assertTrue(Passwords::verify('TEST_USER_PASSWORD', $entity->getPassword()));
        $this->assertEquals($entity->getRole(), $this->role);
        $this->assertEquals($entity->getGroups(), new ArrayCollection());
        $this->assertEquals($entity->getGroupsCreated(), new ArrayCollection());
        $this->assertEquals($entity->getSuperGroupsCreated(), new ArrayCollection());
        $this->assertEquals($entity->getUsersCreated(), new ArrayCollection());
        $this->assertEquals($entity->getCategoriesId(), []);
        $this->assertEquals($entity->getGroupsId(), []);
        $this->assertEquals($entity->isAdmin(), false);
    }

    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $entity = new User();
        $entity->setUsername('TEST_USER_NAME');
        $entity->setPassword('TEST_USER_PASSWORD');
        $entity->setRole($this->role);

        $this->assertInstanceOf(User::class, $entity);

        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 0);
    }

    /**
     * @throws \Exception
     */
    public function testCreateError(): void
    {
        $errorMsgs = [
            0 => "Username can't be blank.",
            1 => "Password can't be blank.",
            2 => "Role can't be blank."
        ];

        $entity = new User();
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 3);

        foreach ($errors as $key => $error){
            $this->assertEquals($errors->get($key)->getMessage(), $errorMsgs[$key]);
        }
    }
}
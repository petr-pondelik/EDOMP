<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.6.19
 * Time: 22:29
 */

namespace AppTests\Model\Entity;


use App\AppTests\Entity\EntityTestCase;
use App\Model\Entity\Role;
use App\Model\Entity\User;

/**
 * Class UserTest
 * @package AppTests\Model\Entity
 */
class UserTest extends EntityTestCase
{
    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $role = new Role();
        $role->setLabel("TEST_ROLE");
        $role->setKey("testrole");

        $entity = new User();
        $entity->setUsername("TEST_USER_NAME");
        $entity->setPassword("TEST_USER_PASSWORD");
        $entity->setRole($role);

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

        foreach ($errors as $key => $error)
            $this->assertEquals($errors->get($key)->getMessage(), $errorMsgs[$key]);
    }
}
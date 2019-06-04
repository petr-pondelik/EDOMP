<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.6.19
 * Time: 22:27
 */

namespace AppTests\Model\Entity;


use App\AppTests\Entity\EntityTestCase;
use App\Model\Entity\Role;

/**
 * Class RoleTest
 * @package AppTests\Model\Entity
 */
class RoleTest extends EntityTestCase
{
    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $entity = new Role();
        $entity->setLabel("TEST_ROLE");
        $entity->setKey("testrole");
        $this->assertInstanceOf(Role::class, $entity);
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 0);
    }

    /**
     * @throws \Exception
     */
    public function testCreateError(): void
    {
        $errorMsgs = [
            0 => "Key can't be blank.",
            1 => "Label can't be blank."
        ];

        $entity = new Role();
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 2);

        foreach ($errors as $key => $error)
            $this->assertEquals($errors->get($key)->getMessage(), $errorMsgs[$key]);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 3.6.19
 * Time: 23:15
 */

namespace AppTests\Model\Entity;


use App\AppTests\Entity\EntityTestCase;
use App\Model\Entity\Group;
use App\Model\Entity\SuperGroup;

/**
 * Class GroupTest
 * @package AppTests\Model\Entity
 */
class GroupTest extends EntityTestCase
{
    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $superGroup = new SuperGroup();
        $superGroup->setLabel("TESTSUPERGROUP");

        $entity = new Group();
        $entity->setLabel("TESTGROUP");
        $entity->setSuperGroup($superGroup);
        $this->assertInstanceOf(Group::class, $entity);
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 0);
    }

    /**
     * @throws \Exception
     */
    public function testCreateError(): void
    {
        $errorMsgs = [
            0 => "SuperGroup can't be blank.",
            1 => "Label can't be blank."
        ];

        $entity = new Group();
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 2);

        foreach ($errors as $key => $error)
            $this->assertEquals($errors->get($key)->getMessage(), $errorMsgs[$key]);
    }
}
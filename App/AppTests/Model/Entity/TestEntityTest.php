<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.6.19
 * Time: 22:26
 */

namespace AppTests\Model\Entity;


use App\AppTests\Entity\EntityTestCase;
use App\Model\Entity\Group;
use App\Model\Entity\Logo;
use App\Model\Entity\SuperGroup;
use App\Model\Entity\Test;

/**
 * Class TestEntityTest
 * @package AppTests\Model\Entity
 */
class TestEntityTest extends EntityTestCase
{
    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $superGroup = new SuperGroup();
        $superGroup->setLabel("TEST_SUPER_GROUP");

        $errors = $this->validator->validate($superGroup);
        $this->assertEquals($errors->count(), 0);

        $group = new Group();
        $group->setLabel("TEST_GROUP");
        $group->setSuperGroup($superGroup);

        $errors = $this->validator->validate($group);
        $this->assertEquals($errors->count(), 0);

        $logo = new Logo();
        $logo->setLabel("TEST_LOGO");
        $logo->setExtensionTmp("TEST_EXTENSION_TMP");

        $errors = $this->validator->validate($logo);
        $this->assertEquals($errors->count(), 0);

        $entity = new Test();
        $entity->setSchoolYear("2018/2019");
        $entity->setTestNumber(1);
        $entity->setTerm("1. pololetí");
        $entity->addGroup($group);
        $entity->setLogo($logo);

        $this->assertInstanceOf(Test::class, $entity);

        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 0);
    }

    /**
     * @throws \Exception
     */
    public function testCreateError(): void
    {
        $errorMsgs = [
            0 => "SchoolYear can't be blank.",
            1 => "TestNumber can't be blank.",
            2 => "Term can't be blank.",
            3 => "Logo can't be blank."
        ];

        $entity = new Test();
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 4);

        foreach ($errors as $key => $error)
            $this->assertEquals($errors->get($key)->getMessage(), $errorMsgs[$key]);

        $entity->setSchoolYear("aaa");
        $errorMsgs[0] = "SchoolYear is not valid.";

        $entity->setTestNumber(-10);
        $errorMsgs[1] = "TestNumber must be greater or equal to 0.";

        $errors = $this->validator->validate($entity);

        foreach ($errors as $key => $error)
            $this->assertEquals($errors->get($key)->getMessage(), $errorMsgs[$key]);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.6.19
 * Time: 22:07
 */

namespace AppTests\Model\Entity;


use App\AppTests\Entity\EntityTestCase;
use App\Model\Entity\Category;
use App\Model\Entity\Difficulty;
use App\Model\Entity\Group;
use App\Model\Entity\Logo;
use App\Model\Entity\ProblemFinal;
use App\Model\Entity\ProblemTestAssociation;
use App\Model\Entity\ProblemType;
use App\Model\Entity\SubCategory;
use App\Model\Entity\SuperGroup;
use App\Model\Entity\Test;

/**
 * Class ProblemTestAssociationTest
 * @package AppTests\Model\Entity
 */
class ProblemTestAssociationTest extends EntityTestCase
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

        $test = new Test();
        $test->setSchoolYear("2018/2019");
        $test->setTestNumber(1);
        $test->setTerm("1. pololetí");
        $test->addGroup($group);
        $test->setLogo($logo);

        $errors = $this->validator->validate($test);
        $this->assertEquals($errors->count(), 0);

        $category = new Category();
        $category->setLabel("TEST_CATEGORY");

        $errors = $this->validator->validate($category);
        $this->assertEquals($errors->count(), 0);

        $subCategory = new SubCategory();
        $subCategory->setLabel("TEST_SUB_CATEGORY");
        $subCategory->setCategory($category);

        $errors = $this->validator->validate($subCategory);
        $this->assertEquals($errors->count(), 0);

        $difficulty = new Difficulty();
        $difficulty->setLabel("TEST_DIFFICULTY");

        $errors = $this->validator->validate($difficulty);
        $this->assertEquals($errors->count(), 0);

        $problemType = new ProblemType();
        $problemType->setLabel("TEST_PROBLEM_TYPE");

        $errors = $this->validator->validate($problemType);
        $this->assertEquals($errors->count(), 0);

        $problemFinal = new ProblemFinal();
        $problemFinal->setBody("TEST_BODY");
        $problemFinal->setSubCategory($subCategory);
        $problemFinal->setDifficulty($difficulty);
        $problemFinal->setProblemType($problemType);

        $errors = $this->validator->validate($problemFinal);
        $this->assertEquals($errors->count(), 0);

        $entity = new ProblemTestAssociation();
        $entity->setTest($test);
        $entity->setVariant("A");
        $entity->setProblem($problemFinal);

        $this->assertInstanceOf(ProblemTestAssociation::class, $entity);
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 0);
    }

    /**
     * @throws \Exception
     */
    public function testCreateError(): void
    {
        $errorMsgs = [
            0 => "Variant can't be blank.",
            1 => "ProblemFinal can't be blank.",
            2 => "Test can't be blank."
        ];

        $entity = new ProblemTestAssociation();

        $this->assertInstanceOf(ProblemTestAssociation::class, $entity);
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 3);

        foreach ($errors as $key => $error)
            $this->assertEquals($error->getMessage(), $errorMsgs[$key]);
    }
}
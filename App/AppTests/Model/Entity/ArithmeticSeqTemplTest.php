<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 3.6.19
 * Time: 22:49
 */

namespace App\AppTests\Entity;

use App\Model\Entity\ArithmeticSeqTempl;
use App\Model\Entity\Category;
use App\Model\Entity\Difficulty;
use App\Model\Entity\ProblemType;
use App\Model\Entity\SubCategory;

/**
 * Class ArithmeticSeqTemplTest
 * @package App\AppTests\Entity
 */
class ArithmeticSeqTemplTest extends EntityTestCase
{
    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $category = new Category();
        $category->setLabel("TESTCATEGORY1");

        $difficulty = new Difficulty();
        $difficulty->setLabel("TESTDIFFICULTY1");

        $subCategory = new SubCategory();
        $subCategory->setLabel("TESTSUBCATEGORY1");
        $subCategory->setCategory($category);

        $problemType = new ProblemType();
        $problemType->setLabel("TESTPROBLEMTYPE1");

        $entity = new ArithmeticSeqTempl();
        $entity->setBody("TESTBODY");
        $entity->setVariable("x");
        $entity->setFirstN(5);
        $entity->setSubCategory($subCategory);
        $entity->setDifficulty($difficulty);
        $entity->setProblemType($problemType);

        $errors = $this->validator->validate($entity);

        $this->assertInstanceOf(ArithmeticSeqTempl::class, $entity);
        $this->assertEquals($errors->count(), 0);
    }

    /**
     * @throws \Exception
     */
    public function testCreateError(): void
    {
        $errorMsgs = [
            0 => "Variable can't be blank.",
            1 => "FirstN can't be blank.",
            2 => "Body can't be blank.",
            3 => "ProblemType can't be blank.",
            4 => "Difficulty can't be blank.",
            5 => "SubCategory can't be blank."
        ];

        $entity = new ArithmeticSeqTempl();

        $errors = $this->validator->validate($entity);

        $this->assertEquals($errors->count(), 6);

        foreach ($errors as $key => $error)
            $this->assertEquals($error->getMessage(), $errorMsgs[$key]);

        $entity->setVariable("aaa");

        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 6);

        $errorMsgs[0] = "Variable must be string of length 1.";

        foreach ($errors as $key => $error)
            $this->assertEquals($error->getMessage(), $errorMsgs[$key]);

        $this->expectException(\TypeError::class);
        $entity->setFirstN("aaa");
    }
}
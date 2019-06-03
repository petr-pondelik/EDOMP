<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 3.6.19
 * Time: 22:40
 */

namespace App\AppTests\Entity;

use App\Model\Entity\Category;
use App\Model\Entity\Difficulty;
use App\Model\Entity\LinearEqTempl;
use App\Model\Entity\ProblemType;
use App\Model\Entity\QuadraticEqTempl;
use App\Model\Entity\SubCategory;

/**
 * Class QuadraticEqTemplTest
 * @package App\AppTests\Entity
 */
class QuadraticEqTemplTest extends EntityTestCase
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

        $entity = new QuadraticEqTempl();
        $entity->setBody("TESTBODY");
        $entity->setVariable("x");
        $entity->setSubCategory($subCategory);
        $entity->setDifficulty($difficulty);
        $entity->setProblemType($problemType);

        $errors = $this->validator->validate($entity);

        $this->assertInstanceOf(QuadraticEqTempl::class, $entity);
        $this->assertEquals($errors->count(), 0);
    }

    /**
     * @throws \Exception
     */
    public function testCreateError(): void
    {
        $errorMsgs = [
            0 => "Variable can't be blank.",
            1 => "Body can't be blank.",
            2 => "ProblemType can't be blank.",
            3 => "Difficulty can't be blank.",
            4 => "SubCategory can't be blank."
        ];

        $entity = new QuadraticEqTempl();

        $errors = $this->validator->validate($entity);

        $this->assertEquals($errors->count(), 5);

        foreach ($errors as $key => $error)
            $this->assertEquals($error->getMessage(), $errorMsgs[$key]);

        $entity->setVariable("aaa");

        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 5);

        $errorMsgs[0] = "Variable must be string of length 1.";

        foreach ($errors as $key => $error)
            $this->assertEquals($error->getMessage(), $errorMsgs[$key]);
    }
}
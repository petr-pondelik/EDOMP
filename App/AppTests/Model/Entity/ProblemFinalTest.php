<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 3.6.19
 * Time: 21:03
 */

namespace App\AppTests\Entity;

use App\Model\Entity\Category;
use App\Model\Entity\Difficulty;
use App\Model\Entity\ProblemFinal;
use App\Model\Entity\ProblemType;
use App\Model\Entity\SubCategory;

/**
 * Class ProblemFinalTest
 * @package App\AppTests\Entity
 */
class ProblemFinalTest extends EntityTestCase
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

        $entity = new ProblemFinal();
        $entity->setBody("TESTBODY");
        $entity->setResult("TESTRESULT");
        $entity->setSubCategory($subCategory);
        $entity->setDifficulty($difficulty);
        $entity->setProblemType($problemType);

        $errors = $this->validator->validate($entity);

        $this->assertInstanceOf(ProblemFinal::class, $entity);
        $this->assertEquals($errors->count(), 0);
    }

    /**
     * @throws \Exception
     */
    public function testCreateError(): void
    {
        $errorMsgs = [
            0 => "Variable must be string of length 1.",
            1 => "Body can't be blank.",
            2 => "ProblemType can't be blank.",
            3 => "Difficulty can't be blank.",
            4 => "SubCategory can't be blank."
        ];

        $entity = new ProblemFinal();
        $entity->setVariable("aaa");

        $errors = $this->validator->validate($entity);

        $this->assertEquals($errors->count(), 5);

        foreach ($errors as $key => $error)
            $this->assertEquals($error->getMessage(), $errorMsgs[$key]);

        $this->expectException(\TypeError::class);
        $entity->setFirstN("aaa");
        $entity->setSuccessRate("aaa");
        $entity->setIsTemplate("aaa");
    }
}
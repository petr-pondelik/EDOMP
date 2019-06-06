<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 3.6.19
 * Time: 21:03
 */

namespace App\AppTests\Entity;

use App\Model\Entity\ProblemFinal;
use AppTests\Model\Entity\ProblemEntityTestCase;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ProblemFinalTest
 * @package App\AppTests\Entity
 */
class ProblemFinalTest extends ProblemEntityTestCase
{
    /**
     * @throws \Exception
     */
    public function testValues(): void
    {
        $entity = new ProblemFinal();
        $entity->setBody('TEST_BODY');
        $entity->setResult('TEST_RESULT');
        $entity->setSubCategory($this->subCategory);
        $entity->setDifficulty($this->difficulty);
        $entity->setProblemType($this->problemType);

        $this->assertEquals($entity->getBody(), 'TEST_BODY');
        $this->assertEquals($entity->getResult(), 'TEST_RESULT');
        $this->assertEquals($entity->getSubCategory(), $this->subCategory);
        $this->assertEquals($entity->getDifficulty(), $this->difficulty);
        $this->assertEquals($entity->getProblemType(), $this->problemType);
        $this->assertEquals($entity->getTextBefore(), null);
        $this->assertEquals($entity->getTextAfter(), null);
        $this->assertEquals($entity->getConditions(), new ArrayCollection());
        $this->assertEquals($entity->getSuccessRate(), null);
        $this->assertEquals($entity->getTestAssociations(), new ArrayCollection());
        $this->assertEquals($entity->getFirstN(), null);
        $this->assertEquals($entity->getVariable(), null);
        $this->assertEquals($entity->getProblemTemplate(), null);
    }

    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $entity = new ProblemFinal();
        $entity->setBody('TEST_BODY');
        $entity->setResult('TEST_RESULT');
        $entity->setSubCategory($this->subCategory);
        $entity->setDifficulty($this->difficulty);
        $entity->setProblemType($this->problemType);

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
            0 => 'Variable must be string of length 1.',
            1 => "Body can't be blank.",
            2 => "ProblemType can't be blank.",
            3 => "Difficulty can't be blank.",
            4 => "SubCategory can't be blank."
        ];

        $entity = new ProblemFinal();
        $entity->setVariable('TEST');

        $errors = $this->validator->validate($entity);

        $this->assertEquals($errors->count(), 5);

        foreach ($errors as $key => $error){
            $this->assertEquals($error->getMessage(), $errorMsgs[$key]);
        }

        $this->expectException(\TypeError::class);
        $entity->setFirstN('TEST');
        $entity->setSuccessRate('TEST');
        $entity->setIsTemplate('TEST');
    }
}
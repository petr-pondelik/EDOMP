<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 3.6.19
 * Time: 22:59
 */

namespace Tests\Model\Entity;

use App\Model\Entity\GeometricSeqTempl;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class GeometricSeqTemplTest
 * @package App\AppTests\Entity
 */
class GeometricSeqTemplTest extends ProblemEntityTestCase
{
    /**
     * @throws \Exception
     */
    public function testValues(): void
    {
        $entity = new GeometricSeqTempl();
        $entity->setBody('TEST_BODY');
        $entity->setVariable('T');
        $entity->setFirstN(5);
        $entity->setSubCategory($this->subCategory);
        $entity->setDifficulty($this->difficulty);
        $entity->setProblemType($this->problemType);

        $this->assertEquals($entity->getBody(), 'TEST_BODY');
        $this->assertEquals($entity->getVariable(), 'T');
        $this->assertEquals($entity->getFirstN(), 5);
        $this->assertEquals($entity->getSubCategory(), $this->subCategory);
        $this->assertEquals($entity->getDifficulty(), $this->difficulty);
        $this->assertEquals($entity->getProblemType(), $this->problemType);
        $this->assertEquals($entity->getTextBefore(), null);
        $this->assertEquals($entity->getTextAfter(), null);
        $this->assertEquals($entity->getConditions(), new ArrayCollection());
        $this->assertEquals($entity->getQuotient(), null);
        $this->assertEquals($entity->getSuccessRate(), null);
        $this->assertEquals($entity->getMatches(), null);
        $this->assertEquals($entity->getTestAssociations(), new ArrayCollection());
    }

    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $entity = new GeometricSeqTempl();
        $entity->setBody('TEST_BODY');
        $entity->setVariable('T');
        $entity->setFirstN(5);
        $entity->setSubCategory($this->subCategory);
        $entity->setDifficulty($this->difficulty);
        $entity->setProblemType($this->problemType);

        $errors = $this->validator->validate($entity);

        $this->assertInstanceOf(GeometricSeqTempl::class, $entity);
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

        $entity = new GeometricSeqTempl();

        $errors = $this->validator->validate($entity);

        $this->assertEquals($errors->count(), 6);

        foreach ($errors as $key => $error){
            $this->assertEquals($error->getMessage(), $errorMsgs[$key]);
        }

        $entity->setVariable('TEST');

        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 6);

        $errorMsgs[0] = 'Variable must be string of length 1.';

        foreach ($errors as $key => $error){
            $this->assertEquals($error->getMessage(), $errorMsgs[$key]);
        }

        $this->expectException(\TypeError::class);
        $entity->setFirstN('TEST');
    }
}
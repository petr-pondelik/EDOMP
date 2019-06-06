<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 3.6.19
 * Time: 19:15
 */

namespace App\AppTests\Entity;

use App\Model\Entity\ProblemCondition;
use App\Model\Entity\ProblemConditionType;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ProblemCondition
 * @package App\AppTests\Entity
 */
class ProblemConditionTest extends EntityTestCase
{
    /**
     * @var ProblemConditionType
     */
    protected $problemConditionType;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $problemConditionType = new ProblemConditionType();
        $problemConditionType->setLabel('TEST_LABEL');
        $this->problemConditionType = $problemConditionType;
    }

    /**
     * @throws \Exception
     */
    public function testValues(): void
    {
        $entity = new ProblemCondition();
        $entity->setLabel('TEST_PROBLEM_CONDITION');
        $entity->setAccessor(1);
        $entity->setProblemConditionType($this->problemConditionType);

        $this->assertEquals($entity->getLabel(), 'TEST_PROBLEM_CONDITION');
        $this->assertEquals($entity->getAccessor(), 1);
        $this->assertEquals($entity->getProblems(), new ArrayCollection());
        $this->assertEquals($entity->getProblemConditionType(), $this->problemConditionType);
    }

    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $entity = new ProblemCondition();
        $entity->setLabel('TEST_PROBLEM_CONDITION');
        $entity->setAccessor(1);
        $entity->setProblemConditionType($this->problemConditionType);

        $this->assertInstanceOf(ProblemCondition::class, $entity);
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 0);
    }

    /**
     * @throws \Exception
     */
    public function testCreateError(): void
    {
        $errorMsgs = [
            0 => "Accessor can't be blank.",
            1 => "ProblemConditionType can't be blank.",
            2 => "Label can't be blank."
        ];

        $entity = new ProblemCondition();
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 3);

        foreach ($errors as $key => $error){
            $this->assertEquals($error->getMessage(), $errorMsgs[$key]);
        }

        $this->expectException(\TypeError::class);
        $entity->setAccessor('TEST');
    }
}
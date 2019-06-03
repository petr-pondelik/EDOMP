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

/**
 * Class ProblemCondition
 * @package App\AppTests\Entity
 */
class ProblemConditionTest extends EntityTestCase
{
    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $entity = new ProblemCondition();
        $entity->setLabel("TESTPROBLEMCONDITION");
        $entity->setAccessor(1);
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
            1 => "Label can't be blank."
        ];

        $entity = new ProblemConditionType();
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 2);

        foreach ($errors as $key => $error)
            $this->assertEquals($error->getMessage(), $errorMsgs[$key]);

        $this->expectException(\TypeError::class);
        $entity->setAccessor("aaa");
    }
}
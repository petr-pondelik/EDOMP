<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 3.6.19
 * Time: 19:15
 */

namespace App\AppTests\Entity;

use App\Model\Entity\ProblemConditionType;

/**
 * Class ProblemConditionType
 * @package App\AppTests\Entity
 */
class ProblemConditionTypeTest extends EntityTestCase
{
    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $entity = new ProblemConditionType();
        $entity->setLabel("TESTPROBLEMCONDITIONTYPE");
        $entity->setAccessor(1);
        $this->assertInstanceOf(ProblemConditionType::class, $entity);
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
    }
}
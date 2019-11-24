<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 3.6.19
 * Time: 19:15
 */

namespace Tests\Model\Entity;

use App\Model\Entity\ProblemConditionType;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ProblemConditionType
 * @package App\AppTests\Entity
 */
class ProblemConditionTypeTest extends EntityTestCase
{
    /**
     * @throws \Exception
     */
    public function testValues(): void
    {
        $entity = new ProblemConditionType();
        $entity->setLabel('TEST_PROBLEM_CONDITION_TYPE');

        $this->assertEquals($entity->getLabel(), 'TEST_PROBLEM_CONDITION_TYPE');
        $this->assertEquals($entity->getProblemTypes(), new ArrayCollection());
    }

    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $entity = new ProblemConditionType();
        $entity->setLabel('TEST_PROBLEM_CONDITION_TYPE');

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
            0 => "Label can't be blank."
        ];

        $entity = new ProblemConditionType();
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 1);

        foreach ($errors as $key => $error)
            $this->assertEquals($error->getMessage(), $errorMsgs[$key]);
    }
}
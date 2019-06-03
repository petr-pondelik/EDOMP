<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 3.6.19
 * Time: 19:11
 */

namespace App\AppTests\Entity;

use App\Model\Entity\ProblemType;

/**
 * Class ProblemTypeTest
 * @package App\AppTests\Entity
 */
class ProblemTypeTest extends EntityTestCase
{
    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $entity = new ProblemType();
        $entity->setLabel("TESTPROBLEMTYPE");
        $this->assertInstanceOf(ProblemType::class, $entity);
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 0);
    }

    /**
     * @throws \Exception
     */
    public function testCreateError(): void
    {
        $entity = new ProblemType();
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 1);
        $this->assertEquals($errors->get(0)->getMessage(), 'Label can\'t be blank.');
    }
}
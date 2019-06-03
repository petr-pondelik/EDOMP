<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 3.6.19
 * Time: 23:10
 */

namespace App\AppTests\Entity;

use App\Model\Entity\Difficulty;

/**
 * Class DifficultyTest
 * @package App\AppTests\Entity
 */
class DifficultyTest extends EntityTestCase
{
    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $entity = new Difficulty();
        $entity->setLabel("TESTDIFFICULTY");
        $this->assertInstanceOf(Difficulty::class, $entity);
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 0);
    }

    /**
     * @throws \Exception
     */
    public function testCreateError(): void
    {
        $entity = new Difficulty();
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 1);
        $this->assertEquals($errors->get(0)->getMessage(), 'Label can\'t be blank.');
    }
}
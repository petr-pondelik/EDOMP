<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 3.6.19
 * Time: 23:10
 */

namespace App\AppTests\Entity;

use App\Model\Entity\Difficulty;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class DifficultyTest
 * @package App\AppTests\Entity
 */
class DifficultyTest extends EntityTestCase
{
    /**
     * @throws \Exception
     */
    public function testValues(): void
    {
        $entity = new Difficulty();
        $entity->setLabel('TEST_DIFFICULTY');

        $this->assertEquals($entity->getLabel(), 'TEST_DIFFICULTY');
        $this->assertEquals($entity->getProblems(), new ArrayCollection());
    }

    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $entity = new Difficulty();
        $entity->setLabel('TEST_DIFFICULTY');
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
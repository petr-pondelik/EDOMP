<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.11.19
 * Time: 21:33
 */

namespace App\Tests\CoreModule\Model\Persistent\Entity;


use App\CoreModule\Model\Persistent\Entity\Difficulty;
use App\Tests\Traits\ProblemFinalSetUpTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class DifficultyTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
final class DifficultyTest extends PersistentEntityTestCase
{
    use ProblemFinalSetUpTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpProblemFinal();
    }

    public function testValidState(): void
    {
        $entity = new Difficulty();

        $entity->setLabel('TEST_LABEL');

        $this->assertEquals($entity->getLabel(), 'TEST_LABEL');
        $this->assertEquals($entity->getProblems(), new ArrayCollection());

        $entity->setProblems(new ArrayCollection([$this->problemFinal]));

        $this->assertEquals(new ArrayCollection([$this->problemFinal]), $entity->getProblems());

        $violations = $this->validator->validate($entity);
        $this->assertCount(0, $violations);
    }

    public function testInvalidState(): void
    {
        $entity = new Difficulty();

        $violations = $this->validator->validate($entity);

        $this->assertCount(1, $violations);
        $this->assertEquals('Label can\'t be blank.', $violations->get(0)->getMessage());

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Return value of App\CoreModule\Model\Persistent\Entity\Difficulty::getLabel() must be of the type string, null returned');
        $entity->getLabel();
    }
}
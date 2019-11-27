<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.11.19
 * Time: 21:33
 */

declare(strict_types = 1);

namespace App\Tests\CoreModule\Model\Persistent\Entity;


use App\CoreModule\Model\Persistent\Entity\Difficulty;
use App\Tests\Traits\ProblemFinalMockSetUpTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class DifficultyTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
final class DifficultyUnitTest extends PersistentEntityTestCase
{
    use ProblemFinalMockSetUpTrait;

    /**
     * @var array
     */
    protected $errorMessages = [
        0 => "Label can't be blank."
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpProblemFinalMock();
    }

    public function testValidState(): void
    {
        $entity = new Difficulty();

        $entity->setLabel('TEST_LABEL');

        $this->assertEquals(false, $entity->isTeacherLevelSecured());
        $this->assertEquals($entity->getLabel(), 'TEST_LABEL');
        $this->assertEquals($entity->getProblems(), new ArrayCollection());

        $entity->setProblems(new ArrayCollection([$this->problemFinalMock]));

        $this->assertEquals(new ArrayCollection([$this->problemFinalMock]), $entity->getProblems());

        $this->assertValidByValidator($entity);
    }

    public function testInvalidState(): void
    {
        $entity = new Difficulty();

        $this->assertValidatorViolations($entity);

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Return value of App\CoreModule\Model\Persistent\Entity\Difficulty::getLabel() must be of the type string, null returned');
        $entity->getLabel();
    }
}
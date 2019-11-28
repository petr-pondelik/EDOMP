<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.11.19
 * Time: 16:56
 */

declare(strict_types = 1);

namespace App\Tests\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Entity\ProblemCondition;
use App\Tests\Traits\ProblemConditionTypeSetUpTrait;

/**
 * Class ProblemConditionUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
final class ProblemConditionUnitTest extends PersistentEntityTestCase
{
    use ProblemConditionTypeSetUpTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpProblemConditionTypeMock();
    }

    /**
     * @var array
     */
    protected $errorMessages = [
        0 => "Accessor can't be blank.",
        1 => "ProblemConditionType can't be blank.",
        2 => "Label can't be blank.",
    ];

    public function testValidState(): void
    {
        $entity = new ProblemCondition();
        $label = 'TEST_LABEL';
        $accessor = 0;

        $this->assertInstanceOf(ProblemCondition::class, $entity);

        $entity->setLabel($label);
        $entity->setAccessor($accessor);
        $entity->setProblemConditionType($this->problemConditionTypeMock);

        $this->assertEquals($label, (string) $entity);
        $this->assertFalse($entity->isTeacherLevelSecured());
        $this->assertEquals($label, $entity->getLabel());
        $this->assertEquals($accessor, $entity->getAccessor());
        $this->assertEquals($entity->getProblemConditionType(), $this->problemConditionTypeMock);
        $this->assertValidByValidator($entity);
    }

    public function testInvalidState(): void
    {
        $entity = new ProblemCondition();
        $this->assertValidatorViolations($entity);
    }
}
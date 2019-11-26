<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.11.19
 * Time: 0:15
 */

namespace App\Tests\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Entity\ProblemType;
use App\Tests\Traits\ProblemConditionTypeSetUpTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ProblemTypeUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
class ProblemTypeUnitTest extends PersistentEntityTestCase
{
    use ProblemConditionTypeSetUpTrait;

    /**
     * @var array
     */
    protected $errorMessages = [
        0 => "Label can't be blank."
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpProblemConditionTypeMock();
    }

    public function testValidState(): void
    {
        $entity = new ProblemType();
        $label = 'TEST_LABEL';
        $keyLabel = 'TEST_KEY_LABEL';
        $problemConditionTypes = new ArrayCollection([$this->problemConditionTypeMock]);

        $entity->setLabel($label);
        $entity->setKeyLabel($keyLabel);

        $this->assertEquals($label, $entity->getLabel());
        $this->assertEquals($keyLabel, $entity->getKeyLabel());

        $entity->setConditionTypes($problemConditionTypes);

        $this->assertCount(1, $entity->getConditionTypes()->getValues());
        $this->assertEquals($problemConditionTypes, $entity->getConditionTypes());

        $this->assertValidByValidator($entity);
    }

    public function testInvalidState(): void
    {
        $entity = new ProblemType();
        $this->assertValidatorViolations($entity);
    }
}
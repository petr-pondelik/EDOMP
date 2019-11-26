<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.11.19
 * Time: 2:01
 */

namespace App\Tests\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Entity\ProblemConditionType;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ProblemConditionTypeUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
class ProblemConditionTypeUnitTest extends PersistentEntityTestCase
{
    /**
     * @var array
     */
    protected $errorMessages = [
        0 => "Label can't be blank."
    ];

    public function testValidState(): void
    {
        $entity = new ProblemConditionType();
        $label = 'TEST_LABEL';

        $entity->setLabel($label);

        $this->assertEquals(false, $entity->isTeacherLevelSecured());
        $this->assertEquals($entity->getLabel(), $label);
        $this->assertEquals($entity->getProblemTypes(), new ArrayCollection());

        $this->assertValidByValidator($entity);
    }

    public function testInvalidState(): void
    {
        $entity = new ProblemConditionType();
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 1);
        $this->assertValidatorViolations($entity);
    }
}
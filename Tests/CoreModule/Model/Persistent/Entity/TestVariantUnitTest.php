<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.11.19
 * Time: 16:17
 */

namespace App\Tests\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Entity\ProblemFinalTestVariantAssociation;
use App\CoreModule\Model\Persistent\Entity\TestVariant;
use App\Tests\Traits\ProblemFinalTestVariantAssociationSetUpMockTrait;
use App\Tests\Traits\TestMockSetUpTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class TestVariantUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
final class TestVariantUnitTest extends PersistentEntityTestCase
{
    use TestMockSetUpTrait;
    use ProblemFinalTestVariantAssociationSetUpMockTrait;

    /**
     * @var array
     */
    protected $errorMessages = [
        "Label can't be blank.",
        "Test can't be blank."
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTestMock();
        $this->setUpProblemFinalTestVariantAssociation();
    }

    public function testValidState(): void
    {
        $entity = new TestVariant();
        $label = 'A';

        $entity->setLabel($label);
        $entity->setTest($this->testMock);

        $this->assertInstanceOf(TestVariant::class, $entity);
        $this->assertFalse($entity->isTeacherLevelSecured());
        $this->assertEquals($label, (string) $entity);
        $this->assertEquals($label, $entity->getLabel());
        $this->assertEquals($this->testMock, $entity->getTest());
        $this->assertEquals(new ArrayCollection(), $entity->getProblemFinalAssociations());

        $entity->setProblemFinalAssociations(new ArrayCollection([$this->problemFinalTestVariantAssociationMock]));
        $this->assertEquals(new ArrayCollection([$this->problemFinalTestVariantAssociationMock]), $entity->getProblemFinalAssociations());

        $entity->addProblemFinalAssociation($this->problemFinalTestVariantAssociationMock);
        $this->assertEquals(new ArrayCollection([$this->problemFinalTestVariantAssociationMock]), $entity->getProblemFinalAssociations());

        /**
         * @var ProblemFinalTestVariantAssociation $problemFinalTestVariantAssociationMockSecond
         */
        $problemFinalTestVariantAssociationMockSecond = $this->getMockBuilder(ProblemFinalTestVariantAssociation::class)->disableOriginalConstructor()->getMock();
        $entity->addProblemFinalAssociation($problemFinalTestVariantAssociationMockSecond);
        $this->assertEquals(new ArrayCollection([$this->problemFinalTestVariantAssociationMock, $problemFinalTestVariantAssociationMockSecond]), $entity->getProblemFinalAssociations());

        $this->assertValidByValidator($entity);
    }

    public function testInvalidState(): void
    {
        $entity = new TestVariant();
        $this->assertValidatorViolations($entity);
    }
}
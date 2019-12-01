<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.11.19
 * Time: 16:01
 */

namespace App\Tests\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Entity\ProblemFinalTestVariantAssociation;
use App\Tests\MockTraits\Entity\ProblemFinalMockSetUpTrait;
use App\Tests\MockTraits\Entity\ProblemTemplateSetUpMockTrait;
use App\Tests\MockTraits\Entity\TestVariantMockSetUpTrait;

/**
 * Class ProblemFinalTestVariantAssociationUniTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
final class ProblemFinalTestVariantAssociationUniTest extends PersistentEntityTestCase
{
    use ProblemFinalMockSetUpTrait;
    use ProblemTemplateSetUpMockTrait;
    use TestVariantMockSetUpTrait;

    /**
     * @var array
     */
    protected $errorMessages = [
        "TestVariant can't be blank.",
        "ProblemFinal can't be blank.",
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpProblemFinalMock();
        $this->setUpProblemTemplateMock();
        $this->setUpTestVariantMock();
    }

    public function testValidState(): void
    {
        $entity = new ProblemFinalTestVariantAssociation();

        $this->assertInstanceOf(ProblemFinalTestVariantAssociation::class, $entity);
        $this->assertFalse($entity->isNextPage());
        $this->assertNull($entity->getSuccessRate());
        $this->assertNull($entity->getProblemTemplate());

        $entity->setTestVariant($this->testVariantMock);
        $entity->setProblemFinal($this->problemFinalMock);
        $entity->setSuccessRate(0.5);

        $this->assertEquals($this->testVariantMock, $entity->getTestVariant());
        $this->assertEquals($this->problemFinalMock, $entity->getProblemFinal());
        $this->assertEquals('0.5', (string) $entity);

        $this->assertValidByValidator($entity);
    }

    public function testInvalidState(): void
    {
        $entity = new ProblemFinalTestVariantAssociation();
        $this->assertValidatorViolations($entity);
    }
}
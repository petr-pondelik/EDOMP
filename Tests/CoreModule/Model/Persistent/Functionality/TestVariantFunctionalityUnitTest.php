<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.12.19
 * Time: 23:00
 */

namespace App\Tests\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\ProblemFinalTestVariantAssociation;
use App\CoreModule\Model\Persistent\Entity\TestVariant;
use App\CoreModule\Model\Persistent\Functionality\TestVariantFunctionality;
use App\Tests\MockTraits\Repository\ProblemFinalRepositoryMockTrait;
use App\Tests\MockTraits\Repository\TestRepositoryMockTrait;
use App\Tests\MockTraits\Repository\TestVariantRepositoryMockTrait;
use Nette\Utils\ArrayHash;

/**
 * Class TestVariantFunctionalityUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Functionality
 */
final class TestVariantFunctionalityUnitTest extends FunctionalityUnitTestCase
{
    use TestVariantRepositoryMockTrait;
    use TestRepositoryMockTrait;
    use ProblemFinalRepositoryMockTrait;

    /**
     * @var TestVariantFunctionality
     */
    protected $functionality;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTestVariantRepositoryMock();
        $this->setUpTestRepositoryMock();
        $this->setUpProblemFinalRepositoryMock();
        $this->functionality = new TestVariantFunctionality($this->em, $this->testVariantRepositoryMock);
    }

    /**
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function testCreate(): void
    {
        // Data For TestVariant create
        $data = ArrayHash::from([
            'variantLabel' => 'TEST_LABEL',
            'test' => $this->firstTest,
            'created' => $this->dateTimeStr
        ]);

        // Prepare expected TestVariant
        $expected = $this->firstTestVariant;
        $expected->setLabel($data['variantLabel']);
        $expected->setTest($data['test']);

        // Create TestVariant and test it against expected
        $created = $this->functionality->create($data);
        $created->setId(1);
        $this->assertEquals($expected, $created);
    }

    public function testUpdate(): void
    {
        $this->assertNull($this->functionality->update(1, []));
    }

    public function testAttachProblem(): void
    {
        // Update TestVariant
        $updated = $this->functionality->attachProblem($this->firstTestVariant, $this->firstProblemFinal, true);

        // Test updated against expected values
        $this->assertCount(1, $updated->getProblemFinalAssociations()->getValues());
        /** @var ProblemFinalTestVariantAssociation $association */
        $association = $updated->getProblemFinalAssociations()->get(0);
        $this->assertInstanceOf(ProblemFinalTestVariantAssociation::class, $association);
        $this->assertEquals($association->getTestVariant(), $this->firstTestVariant);
        $this->assertEquals($association->getProblemFinal(), $this->firstProblemFinal);
        $this->assertTrue($association->isNextPage());
        $this->assertNull($association->getProblemTemplate());
    }

    /**
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function testAttachAssociationFromOriginal(): void
    {
        // Prepare original ProblemFinalTestVariantAssociation
        $original = new ProblemFinalTestVariantAssociation();
        $original->setTestVariant($this->firstTestVariant);
        $original->setProblemFinal($this->firstProblemFinal);
        $original->setNextPage(true);

        // Update TestVariant
        $updated = $this->functionality->attachAssociationFromOriginal($this->secondTestVariant, $original);

        // Test updated against expected values
        $this->assertCount(1, $updated->getProblemFinalAssociations()->getValues());
        /** @var ProblemFinalTestVariantAssociation $association */
        $association = $updated->getProblemFinalAssociations()->get(0);
        $this->assertInstanceOf(ProblemFinalTestVariantAssociation::class, $association);
        $this->assertEquals($this->secondTestVariant, $association->getTestVariant());
        $this->assertEquals($this->firstProblemFinal, $association->getProblemFinal());
        $this->assertTrue($association->isNextPage());
        $this->assertNull($association->getProblemTemplate());
    }
}
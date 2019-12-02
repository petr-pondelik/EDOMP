<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.12.19
 * Time: 20:35
 */

namespace App\Tests\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Functionality\ProblemFinalTestVariantAssociationFunctionality;
use App\Tests\MockTraits\Repository\ProblemFinalRepositoryMockTrait;
use App\Tests\MockTraits\Repository\ProblemFinalTestVariantAssociationRepositoryMockTrait;
use App\Tests\MockTraits\Repository\TestVariantRepositoryMockTrait;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemFinalTestVariantAssociationFunctionalityUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Functionality
 */
class ProblemFinalTestVariantAssociationFunctionalityUnitTest extends FunctionalityUnitTestCase
{
    use ProblemFinalTestVariantAssociationRepositoryMockTrait;
    use TestVariantRepositoryMockTrait;
    use ProblemFinalRepositoryMockTrait;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpProblemFinalTestVariantAssociationRepositoryMock();
        $this->setUpTestVariantRepositoryMock();
        $this->setUpProblemFinalRepositoryMock();
        $this->functionality = new ProblemFinalTestVariantAssociationFunctionality(
            $this->em, $this->problemFinalTestVariantAssociationRepositoryMock
        );

        // SetUp the rest of testing environment
        $problemFinalTestVariantAssociation = $this->firstProblemFinalTestVariantAssociation;
        $problemFinalTestVariantAssociation->setTestVariant($this->firstTestVariant);
        $problemFinalTestVariantAssociation->setProblemFinal($this->firstProblemFinal);
        $this->firstProblemFinalTestVariantAssociation = $problemFinalTestVariantAssociation;

        $this->problemFinalTestVariantAssociationRepositoryMock->expects($this->any())
            ->method('findOneBy')
            ->willReturnCallback(static function ($arg) use ($problemFinalTestVariantAssociation) {
                switch ($arg) {
                    case [
                        'problemFinal.id' => 1,
                        'testVariant.id' => 1
                    ]: return $problemFinalTestVariantAssociation;
                    default: return null;
                }
            });
    }

    public function testCreate(): void
    {
        $this->assertNull($this->functionality->create([]));
    }

    public function testUpdate(): void
    {
        // Data for ProblemFinalTestVariantAssociation update
        $data = ArrayHash::from([
            'testVariant' => 1,
            'successRate' => 0.85
        ]);

        // Prepare expected ProblemFinalTestVariantAssociation
        $expected = $this->firstProblemFinalTestVariantAssociation;
        $expected->setSuccessRate($data['successRate']);

        // Update and test against expected
        $updated = $this->functionality->update(1, $data);
        $this->assertEquals($expected, $updated);

        // Test invalid update
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Association for update not found.');
        $this->functionality->update(50, $data);
    }
}
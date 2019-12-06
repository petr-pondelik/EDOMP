<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.12.19
 * Time: 15:48
 */

namespace App\Tests\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\ProblemFinal;
use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use App\CoreModule\Model\Persistent\Functionality\ProblemFunctionality;
use App\Tests\MockTraits\Repository\ProblemFinalTestVariantAssociationRepositoryMockTrait;
use App\Tests\MockTraits\Repository\ProblemRepositoryMockTrait;
use App\Tests\MockTraits\Repository\TestVariantRepositoryMockTrait;

/**
 * Class ProblemFunctionalityUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Functionality
 */
final class ProblemFunctionalityUnitTest extends ProblemFunctionalityUnitTestCase
{
    use ProblemFinalTestVariantAssociationRepositoryMockTrait;
    use ProblemRepositoryMockTrait;
    use TestVariantRepositoryMockTrait;

    /**
     * @var ProblemFunctionality
     */
    protected $functionality;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpProblemFinalTestVariantAssociationRepositoryMock();
        $this->setUpProblemRepositoryMock();
        $this->setUpTestVariantRepositoryMock();

        // Prepare first ProblemTestAssociation
        $firstProblemTestAssociation = $this->firstProblemFinalTestVariantAssociation;
        $firstProblemTestAssociation->setProblemFinal($this->secondProblem);
        $firstProblemTestAssociation->setProblemTemplate($this->firstProblem);
        $firstProblemTestAssociation->setTestVariant($this->firstTestVariant);

        // Prepare second ProblemTestAssociation
        $secondProblemTestAssociation = $this->secondProblemFinalTestVariantAssociation;
        $secondProblemTestAssociation->setProblemFinal($this->secondProblem);
        $secondProblemTestAssociation->setProblemTemplate($this->firstProblem);
        $secondProblemTestAssociation->setTestVariant($this->firstTestVariant);

        // Prepare third ProblemTestAssociation
        $thirdProblemTestAssociation = $this->thirdProblemFinalTestVariantAssociation;
        $thirdProblemTestAssociation->setProblemFinal($this->thirdProblem);
        $thirdProblemTestAssociation->setTestVariant($this->firstTestVariant);

        // Prepare fourth ProblemTestAssociation
        $fourthProblemTestAssociation = $this->fourthProblemFinalTestVariantAssociation;
        $fourthProblemTestAssociation->setProblemFinal($this->thirdProblem);
        $fourthProblemTestAssociation->setTestVariant($this->firstTestVariant);

        // Prepare fifth ProblemTestAssociation
        $fifthProblemTestAssociation = $this->fifthProblemFinalTestVariantAssociation;
        $fifthProblemTestAssociation->setProblemFinal($this->fourthProblem);
        $fifthProblemTestAssociation->setProblemTemplate($this->firstProblemTemplate);
        $fifthProblemTestAssociation->setTestVariant($this->firstTestVariant);

        // Set expected return values for ProblemTestAssociationRepository
        $this->problemFinalTestVariantAssociationRepositoryMock->method('findBy')
            ->willReturnCallback(static function ($arg) use (
                $firstProblemTestAssociation, $secondProblemTestAssociation, $thirdProblemTestAssociation,
                $fourthProblemTestAssociation, $fifthProblemTestAssociation
            ) {
                switch ($arg){
                    case ['problemTemplate.id' => 1]:
                        return [$firstProblemTestAssociation, $secondProblemTestAssociation, $fifthProblemTestAssociation];
                    case ['problemFinal.id' => 2]:
                        return [$firstProblemTestAssociation, $secondProblemTestAssociation];
                    case ['problemFinal.id' => 3]:
                        return [$thirdProblemTestAssociation, $fourthProblemTestAssociation];
                    case ['problemFinal.id' => 4]:
                        return [$fifthProblemTestAssociation];
                }
                return null;
            });

        $this->functionality = new ProblemFunctionality(
            $this->em, $this->problemRepositoryMock, $this->problemFinalTestVariantAssociationRepositoryMock
        );
    }

    public function testCreate(): void
    {
        $this->assertNull($this->functionality->create([]));
    }

    /**
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function testUpdate(): void
    {
        $successRate = 0.4;
        $updated = $this->functionality->update(1, [ 'successRate' => $successRate ]);
        $this->assertEquals($successRate, $updated->getSuccessRate());
    }

    /**
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function testCalculateSuccessRate(): void
    {
        // Calculate success rate for ProblemTemplate
        $this->functionality->calculateSuccessRate(1, true);

        // Test expected success rate value
        /** @var ProblemTemplate $problemTemplate */
        $problemTemplate = $this->problemRepositoryMock->find(1);
        $this->assertEquals(0.58, $problemTemplate->getSuccessRate());

        // Calculate success rate for first ProblemFinal
        $this->functionality->calculateSuccessRate(2);

        // Test expected success rate value
        /** @var ProblemFinal $problemFinal */
        $problemFinal = $this->problemRepositoryMock->find(2);
        $this->assertEquals(0.63, $problemFinal->getSuccessRate());

        // Calculate success rate for second ProblemFinal
        $this->functionality->calculateSuccessRate(3);

        // Test expected success rate value
        $problemFinal = $this->problemRepositoryMock->find(3);
        $this->assertEquals(0.38, $problemFinal->getSuccessRate());

        // Calculate success rate for third ProblemFinal
        $this->functionality->calculateSuccessRate(4);

        //Test expected success rate value
        $problemFinal = $this->problemRepositoryMock->find(4);
        $this->assertEquals(0.5, $problemFinal->getSuccessRate());
    }
}
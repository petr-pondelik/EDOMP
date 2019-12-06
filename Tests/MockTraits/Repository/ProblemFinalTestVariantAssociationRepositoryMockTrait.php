<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.12.19
 * Time: 20:48
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemFinalTestVariantAssociation;
use App\CoreModule\Model\Persistent\Repository\ProblemFinalTestVariantAssociationRepository;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait ProblemFinalTestVariantAssociationRepositoryMock
 * @package App\Tests\MockTraits\Repository
 */
trait ProblemFinalTestVariantAssociationRepositoryMockTrait
{
    /**
     * @var MockObject
     */
    protected $problemFinalTestVariantAssociationRepositoryMock;

    /**
     * @var ProblemFinalTestVariantAssociation
     */
    protected $firstProblemFinalTestVariantAssociation;

    /**
     * @var ProblemFinalTestVariantAssociation
     */
    protected $secondProblemFinalTestVariantAssociation;

    /**
     * @var ProblemFinalTestVariantAssociation
     */
    protected $thirdProblemFinalTestVariantAssociation;

    /**
     * @var ProblemFinalTestVariantAssociation
     */
    protected $fourthProblemFinalTestVariantAssociation;

    /**
     * @var ProblemFinalTestVariantAssociation
     */
    protected $fifthProblemFinalTestVariantAssociation;


    /**
     * @throws \Exception
     */
    protected function setUpProblemFinalTestVariantAssociationRepositoryMock(): void
    {
        $this->problemFinalTestVariantAssociationRepositoryMock = $this->getMockBuilder(ProblemFinalTestVariantAssociationRepository::class)
            ->setMethods(['find', 'findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first ProblemFinalTestVariantAssociation
        $firstProblemFinalTestVariantAssociation = new ProblemFinalTestVariantAssociation();
        $firstProblemFinalTestVariantAssociation->setId(1);
        $firstProblemFinalTestVariantAssociation->setSuccessRate(0.5);
        $firstProblemFinalTestVariantAssociation->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->firstProblemFinalTestVariantAssociation = $firstProblemFinalTestVariantAssociation;

        // Create second ProblemFinalTestVariantAssociation
        $secondProblemFinalTestVariantAssociation = new ProblemFinalTestVariantAssociation();
        $secondProblemFinalTestVariantAssociation->setId(2);
        $secondProblemFinalTestVariantAssociation->setSuccessRate(0.75);
        $secondProblemFinalTestVariantAssociation->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->secondProblemFinalTestVariantAssociation = $secondProblemFinalTestVariantAssociation;

        // Prepare third ProblemFinalTestVariantAssociation
        $thirdProblemFinalTestVariantAssociation = new ProblemFinalTestVariantAssociation();
        $thirdProblemFinalTestVariantAssociation->setId(3);
        $thirdProblemFinalTestVariantAssociation->setSuccessRate(0.25);
        $thirdProblemFinalTestVariantAssociation->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->thirdProblemFinalTestVariantAssociation = $thirdProblemFinalTestVariantAssociation;

        // Prepare fourth ProblemFinalTestVariantAssociation
        $fourthProblemFinalTestVariantAssociation = new ProblemFinalTestVariantAssociation();
        $fourthProblemFinalTestVariantAssociation->setId(4);
        $fourthProblemFinalTestVariantAssociation->setSuccessRate(0.5);
        $fourthProblemFinalTestVariantAssociation->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->fourthProblemFinalTestVariantAssociation = $fourthProblemFinalTestVariantAssociation;

        // Prepare fifth ProblemFinalTestVariantAssociation
        $fifthProblemFinalTestVariantAssociation = new ProblemFinalTestVariantAssociation();
        $fifthProblemFinalTestVariantAssociation->setId(5);
        $fifthProblemFinalTestVariantAssociation->setSuccessRate(0.5);
        $fifthProblemFinalTestVariantAssociation->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->fifthProblemFinalTestVariantAssociation = $fifthProblemFinalTestVariantAssociation;

        // Set LogoRepository expected return values for find
        $this->problemFinalTestVariantAssociationRepositoryMock->method('find')
            ->willReturnCallback(static function ($arg) use (
                $firstProblemFinalTestVariantAssociation, $secondProblemFinalTestVariantAssociation,
                $thirdProblemFinalTestVariantAssociation, $fourthProblemFinalTestVariantAssociation,
                $fifthProblemFinalTestVariantAssociation
            ) {
                switch ($arg) {
                    case 1: return $firstProblemFinalTestVariantAssociation;
                    case 2: return $secondProblemFinalTestVariantAssociation;
                    case 3: return $thirdProblemFinalTestVariantAssociation;
                    case 4: return $fourthProblemFinalTestVariantAssociation;
                    case 5: return $fifthProblemFinalTestVariantAssociation;
                    default: return null;
                }
            });
    }
}
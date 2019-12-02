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
     * @throws \Exception
     */
    protected function setUpProblemFinalTestVariantAssociationRepositoryMock(): void
    {
        $this->problemFinalTestVariantAssociationRepositoryMock = $this->getMockBuilder(ProblemFinalTestVariantAssociationRepository::class)
            ->setMethods(['find', 'findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first ProblemFinalTestVariantAssociation
        $firstProblemFinalTestVariantAssociation = new ProblemFinalTestVariantAssociation();
        $firstProblemFinalTestVariantAssociation->setId(1);
        $firstProblemFinalTestVariantAssociation->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->firstProblemFinalTestVariantAssociation = $firstProblemFinalTestVariantAssociation;

        // Create second ProblemFinalTestVariantAssociation
        $secondProblemFinalTestVariantAssociation = new ProblemFinalTestVariantAssociation();
        $secondProblemFinalTestVariantAssociation->setId(2);
        $secondProblemFinalTestVariantAssociation->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->secondProblemFinalTestVariantAssociation = $secondProblemFinalTestVariantAssociation;

        // Set LogoRepository expected return values for find
        $this->problemFinalTestVariantAssociationRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($firstProblemFinalTestVariantAssociation, $secondProblemFinalTestVariantAssociation) {
                switch ($arg) {
                    case 1: return $firstProblemFinalTestVariantAssociation;
                    case 2: return $secondProblemFinalTestVariantAssociation;
                    default: return null;
                }
            });
    }
}
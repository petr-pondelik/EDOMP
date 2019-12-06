<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.12.19
 * Time: 12:58
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemType;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait ProblemTypeRepositoryMockTrait
 * @package App\Tests\MockTraits\Repository
 */
trait ProblemTypeRepositoryMockTrait
{
    /**
     * @var MockObject
     */
    protected $problemTypeRepositoryMock;

    /**
     * @var ProblemType
     */
    protected $firstProblemType;

    /**
     * @var ProblemType
     */
    protected $secondProblemType;

    /**
     * @throws \Exception
     */
    protected function setUpProblemTypeRepositoryMock(): void
    {
        $this->problemTypeRepositoryMock = $this->getMockBuilder(ProblemTypeRepository::class)
            ->setMethods(['find', 'findAssoc'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first ProblemType
        $firstProblemType = new ProblemType();
        $firstProblemType->setId(1);
        $firstProblemType->setLabel('TEST_FIRST_PROBLEM_TYPE');
        $firstProblemType->setKeyLabel('testFirstProblemType');
        $firstProblemType->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->firstProblemType = $firstProblemType;

        // Create second ProblemType
        $secondProblemType = new ProblemType();
        $secondProblemType->setId(2);
        $secondProblemType->setLabel('TEST_SECOND_PROBLEM_TYPE');
        $secondProblemType->setKeyLabel('testSecondProblemType');
        $secondProblemType->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->secondProblemType = $secondProblemType;

        // Set ProblemTypeRepository expected return values for find
        $this->problemTypeRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($firstProblemType, $secondProblemType) {
                switch ($arg) {
                    case 1: return $firstProblemType;
                    case 2: return $secondProblemType;
                    default: return null;
                }
            });

        // Set ProblemTypeRepository expected return values for findAssoc
        $this->problemTypeRepositoryMock->expects($this->any())
            ->method('findAssoc')
            ->willReturnCallback(static function ($arg) use ($firstProblemType, $secondProblemType) {
                switch ($arg) {
                    case []: return [
                        $firstProblemType->getId() => $firstProblemType,
                        $secondProblemType->getId() => $secondProblemType
                    ];
                    default: return null;
                }
            });
    }
}
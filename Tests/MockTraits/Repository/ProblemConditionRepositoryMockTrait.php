<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.12.19
 * Time: 12:58
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemCondition;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionRepository;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait ProblemConditionRepositoryMockTrait
 * @package App\Tests\MockTraits\Repository
 */
trait ProblemConditionRepositoryMockTrait
{
    /**
     * @var MockObject
     */
    protected $problemConditionRepositoryMock;

    /**
     * @var ProblemCondition
     */
    protected $firstProblemCondition;

    /**
     * @var ProblemCondition
     */
    protected $secondProblemCondition;

    /**
     * @throws \Exception
     */
    protected function setUpProblemConditionRepositoryMock(): void
    {
        $this->problemConditionRepositoryMock = $this->getMockBuilder(ProblemConditionRepository::class)
            ->setMethods(['find', 'findAssocByTypeAndAccessor'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first ProblemCondition
        $firstProblemCondition = new ProblemCondition();
        $firstProblemCondition->setId(1);
        $firstProblemCondition->setLabel('TEST_FIRST_PROBLEM_CONDITION');
        $firstProblemCondition->setAccessor(0);
        $firstProblemCondition->setCreated(DateTime::from('2019-11-29 16:10:40'));

        // Create second ProblemCondition
        $secondProblemCondition = new ProblemCondition();
        $secondProblemCondition->setId(2);
        $secondProblemCondition->setLabel('TEST_SECOND_PROBLEM_CONDITION');
        $secondProblemCondition->setAccessor(1);
        $secondProblemCondition->setCreated(DateTime::from('2019-11-29 16:10:40'));

        // Set ProblemConditionRepository expected return values for find
        $this->problemConditionRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($firstProblemCondition, $secondProblemCondition) {
                switch ($arg) {
                    case 1: return $firstProblemCondition;
                    case 2: return $secondProblemCondition;
                    default: return null;
                }
            });

        // Set ProblemConditionRepository expected return values for findAssoc
        $this->problemConditionRepositoryMock->expects($this->any())
            ->method('findAssocByTypeAndAccessor')
            ->willReturnCallback(static function () use ($firstProblemCondition, $secondProblemCondition) {
                    return [
                        1 => [
                            0 => $firstProblemCondition,
                            1 => $secondProblemCondition
                        ]
                    ];
            });
    }
}
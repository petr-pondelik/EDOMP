<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 2.12.19
 * Time: 0:23
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemConditionType;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionTypeRepository;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait ProblemConditionTypeRepositoryMockTrait
 * @package App\Tests\MockTraits\Repository
 */
trait ProblemConditionTypeRepositoryMockTrait
{
    /**
     * @var MockObject
     */
    protected $problemConditionTypeRepositoryMock;

    /**
     * @var ProblemConditionType
     */
    protected $firstProblemConditionType;

    /**
     * @var ProblemConditionType
     */
    protected $secondProblemConditionType;

    /**
     * @throws \Exception
     */
    protected function setUpProblemConditionTypeRepositoryMock(): void
    {
        $this->problemConditionTypeRepositoryMock = $this->getMockBuilder(ProblemConditionTypeRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first ProblemConditionType
        $firstProblemConditionType = new ProblemConditionType();
        $firstProblemConditionType->setId(1);
        $firstProblemConditionType->setLabel('TEST_FIRST_PROBLEM_CONDITION');
        $firstProblemConditionType->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->firstProblemConditionType = $firstProblemConditionType;

        // Create second ProblemConditionType
        $secondProblemConditionType = new ProblemConditionType();
        $secondProblemConditionType->setId(2);
        $secondProblemConditionType->setLabel('TEST_SECOND_PROBLEM_CONDITION');
        $secondProblemConditionType->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->secondProblemConditionType = $secondProblemConditionType;

        // Set ProblemConditionTypeRepository expected return values for find
        $this->problemConditionTypeRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($firstProblemConditionType, $secondProblemConditionType) {
                switch ($arg) {
                    case 1: return $firstProblemConditionType;
                    case 2: return $secondProblemConditionType;
                    default: return null;
                }
            });
    }
}
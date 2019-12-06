<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.12.19
 * Time: 20:53
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemFinal;
use App\CoreModule\Model\Persistent\Repository\ProblemFinalRepository;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait ProblemFinalRepositoryMockTrait
 * @package App\Tests\MockTraits\Repository
 */
trait ProblemFinalRepositoryMockTrait
{
    /**
     * @var MockObject
     */
    protected $problemFinalRepositoryMock;

    /**
     * @var ProblemFinal
     */
    protected $firstProblemFinal;

    /**
     * @var ProblemFinal
     */
    protected $secondProblemFinal;

    /**
     * @throws \Exception
     */
    protected function setUpProblemFinalRepositoryMock(): void
    {
        $this->problemFinalRepositoryMock = $this->getMockBuilder(ProblemFinalRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first ProblemFinal
        $firstProblemFinal = new ProblemFinal();
        $firstProblemFinal->setId(1);
        $firstProblemFinal->setBody('TEST_BODY_FIRST');
        $firstProblemFinal->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->firstProblemFinal = $firstProblemFinal;

        // Create second ProblemFinal
        $secondProblemFinal = new ProblemFinal();
        $secondProblemFinal->setId(2);
        $secondProblemFinal->setBody('TEST_BODY_SECOND');
        $secondProblemFinal->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->secondProblemFinal = $secondProblemFinal;

        // Set ProblemFinalRepository expected return values for find
        $this->problemFinalRepositoryMock->method('find')
            ->willReturnCallback(static function ($arg) use ($firstProblemFinal, $secondProblemFinal) {
                switch ($arg) {
                    case 1: return $firstProblemFinal;
                    case 2: return $secondProblemFinal;
                    default: return null;
                }
            });
    }
}
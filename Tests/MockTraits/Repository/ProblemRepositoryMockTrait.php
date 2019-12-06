<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.12.19
 * Time: 15:59
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemFinal;
use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\LinearEquationTemplate;
use App\CoreModule\Model\Persistent\Repository\ProblemRepository;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait ProblemRepositoryMockTrait
 * @package App\Tests\MockTraits\Repository
 */
trait ProblemRepositoryMockTrait
{
    /**
     * @var MockObject
     */
    protected $problemRepositoryMock;

    /**
     * @var LinearEquationTemplate
     */
    protected $firstProblem;

    /**
     * @var ProblemFinal
     */
    protected $secondProblem;

    /**
     * @var ProblemFinal
     */
    protected $thirdProblem;

    /**
     * @var ProblemFinal
     */
    protected $fourthProblem;

    protected function setUpProblemRepositoryMock(): void
    {
        $this->problemRepositoryMock = $this->getMockBuilder(ProblemRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Create ProblemTemplate
        $problemTemplate = new LinearEquationTemplate();
        $problemTemplate->setId(1);
        $problemTemplate->setBody('TEST_LINEAR_EQUATION_TEMPLATE');
        $problemTemplate->setVariable('x');
        $this->firstProblem = $problemTemplate;

        // Create first ProblemFinal
        $firstProblemFinal = new ProblemFinal();
        $firstProblemFinal->setId(2);
        $firstProblemFinal->setBody('TEST_FIRST_PROBLEM_FINAL');
        $firstProblemFinal->setProblemTemplate($problemTemplate);
        $this->secondProblem = $firstProblemFinal;

        // Create second ProblemFinal
        $secondProblemFinal = new ProblemFinal();
        $secondProblemFinal->setId(3);
        $secondProblemFinal->setBody('TEST_SECOND_PROBLEM_FINAL');
        $this->thirdProblem = $secondProblemFinal;

        // Create third ProblemFinal
        $thirdProblemFinal = new ProblemFinal();
        $thirdProblemFinal->setId(4);
        $thirdProblemFinal->setProblemTemplate($problemTemplate);
        $thirdProblemFinal->setBody('TEST_THIRD_PROBLEM_FINAL');
        $this->fourthProblem = $thirdProblemFinal;

        // Set expected return values for ProblemRepository
        $this->problemRepositoryMock->method('find')
            ->willReturnCallback(static function ($arg) use (
                $problemTemplate, $firstProblemFinal, $secondProblemFinal, $thirdProblemFinal
            ) {
                switch ($arg) {
                    case 1: return $problemTemplate;
                    case 2: return $firstProblemFinal;
                    case 3: return $secondProblemFinal;
                    case 4: return $thirdProblemFinal;
                    default: return null;
                }
            });

    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 2.12.19
 * Time: 0:16
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\ProblemTemplateRepository;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait ProblemTemplateRepository
 * @package App\Tests\MockTraits\Repository
 */
trait ProblemTemplateRepositoryMockTrait
{
    /**
     * @var MockObject
     */
    protected $problemTemplateRepositoryMock;

    /**
     * @var ProblemTemplate
     */
    protected $firstProblemTemplate;

    /**
     * @var ProblemTemplate
     */
    protected $secondProblemTemplate;

    /**
     * @throws \Exception
     */
    protected function setUpProblemTemplateRepositoryMock(): void
    {
        $this->problemTemplateRepositoryMock = $this->getMockBuilder(ProblemTemplateRepository::class)
            ->setMethods(['find', 'getSequenceVal'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first ProblemTemplate
        $firstProblemTemplate = new ProblemTemplate();
        $firstProblemTemplate->setId(1);
        $firstProblemTemplate->setBody('TEST_BODY_FIRST');
        $firstProblemTemplate->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->firstProblemTemplate = $firstProblemTemplate;

        // Create second ProblemTemplate
        $secondProblemTemplate = new ProblemTemplate();
        $secondProblemTemplate->setId(2);
        $secondProblemTemplate->setBody('TEST_BODY_SECOND');
        $secondProblemTemplate->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->secondProblemTemplate = $secondProblemTemplate;

        // Set ProblemTemplateRepository expected return values for find
        $this->problemTemplateRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($firstProblemTemplate, $secondProblemTemplate) {
                switch ($arg) {
                    case 1: return $firstProblemTemplate;
                    case 2: return $secondProblemTemplate;
                    default: return null;
                }
            });

        // Set ProblemTemplateRepository expected return value for getSequenceVal
        $this->problemTemplateRepositoryMock->method('getSequenceVal')
            ->willReturnCallback(static function () { return 3; });
    }
}
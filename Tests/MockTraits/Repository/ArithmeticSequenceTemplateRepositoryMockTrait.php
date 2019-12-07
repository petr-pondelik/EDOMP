<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.12.19
 * Time: 11:16
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ArithmeticSequenceTemplate;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\ArithmeticSequenceTemplateRepository;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait ArithmeticSequenceTemplateRepositoryMockTrait
 * @package App\Tests\MockTraits\Repository
 */
trait ArithmeticSequenceTemplateRepositoryMockTrait
{
    /**
     * @var MockObject
     */
    protected $arithmeticSequenceTemplateRepositoryMock;

    /**
     * @var ArithmeticSequenceTemplate
     */
    protected $firstArithmeticSequenceTemplate;

    /**
     * @var ArithmeticSequenceTemplate
     */
    protected $secondArithmeticSequenceTemplate;

    protected function setUpArithmeticSequenceTemplateRepositoryMock(): void
    {
        // Mock the ArithmeticSequenceTemplateRepository
        $this->arithmeticSequenceTemplateRepositoryMock = $this->getMockBuilder(ArithmeticSequenceTemplateRepository::class)
            ->setMethods(['find', 'getSequenceVal'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first ArithmeticSequenceTemplate
        $firstArithmeticSequenceTemplate = new ArithmeticSequenceTemplate();
        $firstArithmeticSequenceTemplate->setId(1);
        $firstArithmeticSequenceTemplate->setBody('TEST_ARITHMETIC_SEQUENCE_TEMPLATE_FIRST');
        $firstArithmeticSequenceTemplate->setIndexVariable('n');
        $firstArithmeticSequenceTemplate->setFirstN(5);
        $this->firstArithmeticSequenceTemplate = $firstArithmeticSequenceTemplate;

        $secondArithmeticSequenceTemplate = new ArithmeticSequenceTemplate();
        $secondArithmeticSequenceTemplate->setId(2);
        $secondArithmeticSequenceTemplate->setBody('TEST_ARITHMETIC_SEQUENCE_TEMPLATE_SECOND');
        $secondArithmeticSequenceTemplate->setIndexVariable('m');
        $secondArithmeticSequenceTemplate->setFirstN(10);
        $this->secondArithmeticSequenceTemplate = $secondArithmeticSequenceTemplate;

        // Set expected return values for repository find method
        $this->arithmeticSequenceTemplateRepositoryMock->method('find')
            ->willReturnCallback(static function ($arg) use (
                $firstArithmeticSequenceTemplate, $secondArithmeticSequenceTemplate
            ) {
                switch ($arg) {
                    case 1: return $firstArithmeticSequenceTemplate;
                    case 2: return $secondArithmeticSequenceTemplate;
                    default: return null;
                }
            });
    }
}
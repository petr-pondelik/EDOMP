<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.12.19
 * Time: 21:01
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\Test;
use App\CoreModule\Model\Persistent\Repository\TestRepository;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait TestRepositoryMockTrait
 * @package App\Tests\MockTraits\Repository
 */
trait TestRepositoryMockTrait
{
    /**
     * @var MockObject
     */
    protected $testRepositoryMock;

    /**
     * @var Test
     */
    protected $firstTest;

    /**
     * @var Test
     */
    protected $secondTest;

    /**
     * @throws \Exception
     */
    protected function setUpTestRepositoryMock(): void
    {
        $this->testRepositoryMock = $this->getMockBuilder(TestRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first Test
        $firstTest = new Test();
        $firstTest->setId(1);
        $firstTest->setTerm('TEST_TERM_FIRST');
        $firstTest->setSchoolYear('2018/2019');
        $firstTest->setTestNumber(1);
        $firstTest->setIntroductionText('INTRODUCTION_TEXT_FIRST');
        $firstTest->setProblemsPerVariant(1);
        $firstTest->setVariantsCnt(1);
        $firstTest->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->firstTest = $firstTest;

        // Create second Test
        $secondTest = new Test();
        $secondTest->setId(2);
        $secondTest->setTerm('TEST_TERM_SECOND');
        $secondTest->setSchoolYear('2019/2020');
        $secondTest->setTestNumber(2);
        $secondTest->setIntroductionText('INTRODUCTION_TEXT_SECOND');
        $secondTest->setProblemsPerVariant(1);
        $secondTest->setVariantsCnt(1);
        $secondTest->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->secondTest = $secondTest;

        // Set TestRepository expected return values for find
        $this->testRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($firstTest, $secondTest) {
                switch ($arg) {
                    case 1: return $firstTest;
                    case 2: return $secondTest;
                    default: return null;
                }
            });
    }
}
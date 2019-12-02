<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.12.19
 * Time: 21:11
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\TestVariant;
use App\CoreModule\Model\Persistent\Repository\TestVariantRepository;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait TestVariantRepositoryMock
 * @package App\Tests\MockTraits\Repository
 */
trait TestVariantRepositoryMockTrait
{
    /**
     * @var MockObject
     */
    protected $testVariantRepositoryMock;

    /**
     * @var TestVariant
     */
    protected $firstTestVariant;

    /**
     * @var TestVariant
     */
    protected $secondTestVariant;

    /**
     * @throws \Exception
     */
    protected function setUpTestVariantRepositoryMock(): void
    {
        $this->testVariantRepositoryMock = $this->getMockBuilder(TestVariantRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first TestVariant
        $firstTestVariant = new TestVariant();
        $firstTestVariant->setId(1);
        $firstTestVariant->setLabel('A');
        $firstTestVariant->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->firstTestVariant = $firstTestVariant;

        // Create second TestVariant
        $secondTestVariant = new TestVariant();
        $secondTestVariant->setId(2);
        $secondTestVariant->setLabel('B');
        $secondTestVariant->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->secondTestVariant = $secondTestVariant;

        // Set TestVariantRepository expected return values for find
        $this->testVariantRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($firstTestVariant, $secondTestVariant) {
                switch ($arg) {
                    case 1: return $firstTestVariant;
                    case 2: return $secondTestVariant;
                    default: return null;
                }
            });
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.12.19
 * Time: 15:14
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\SuperGroup;
use App\CoreModule\Model\Persistent\Repository\SuperGroupRepository;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait SuperGroupRepositoryMockTrait
 * @package App\Tests\MockTraits\Repository
 */
trait SuperGroupRepositoryMockTrait
{
    /**
     * @var MockObject
     */
    protected $superGroupRepositoryMock;

    /**
     * @var SuperGroup
     */
    protected $firstSuperGroup;

    /**
     * @var SuperGroup
     */
    protected $secondSuperGroup;

    /**
     * @throws \Exception
     */
    protected function setUpSuperGroupRepositoryMock(): void
    {
        $this->superGroupRepositoryMock = $this->getMockBuilder(SuperGroupRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first SuperGroup
        $firstSuperGroup = new SuperGroup();
        $firstSuperGroup->setId(1);
        $firstSuperGroup->setLabel('TEST_SUPER_GROUP_FIRST');
        $firstSuperGroup->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->firstSuperGroup = $firstSuperGroup;

        // Create second SuperGroup
        $secondSuperGroup = new SuperGroup();
        $secondSuperGroup->setId(2);
        $secondSuperGroup->setLabel('TEST_SUPER_GROUP_SECOND');
        $secondSuperGroup->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->secondSuperGroup = $secondSuperGroup;

        // Set SuperGroupRepository expected return values for find
        $this->superGroupRepositoryMock->method('find')
            ->willReturnCallback(static function ($arg) use ($firstSuperGroup, $secondSuperGroup) {
                switch ($arg) {
                    case 1: return $firstSuperGroup;
                    case 2: return $secondSuperGroup;
                    default: return null;
                }
            });
    }
}
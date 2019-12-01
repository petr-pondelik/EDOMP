<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.12.19
 * Time: 15:08
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\Group;
use App\CoreModule\Model\Persistent\Repository\GroupRepository;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait GroupRepositoryMockTrait
 * @package App\Tests\MockTraits\Repository
 */
trait GroupRepositoryMockTrait
{
    /**
     * @var MockObject
     */
    protected $groupRepositoryMock;

    /**
     * @var Group
     */
    protected $firstGroup;

    /**
     * @var Group
     */
    protected $secondGroup;

    /**
     * @throws \Exception
     */
    protected function setUpGroupRepositoryMock(): void
    {
        $this->groupRepositoryMock = $this->getMockBuilder(GroupRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first Group
        $firstGroup = new Group();
        $firstGroup->setId(1);
        $firstGroup->setLabel('TEST_FIRST_GROUP');
        $firstGroup->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->firstGroup = $firstGroup;

        // Create second Group
        $secondGroup = new Group();
        $secondGroup->setId(2);
        $secondGroup->setLabel('TEST_SECOND_GROUP');
        $secondGroup->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->secondGroup = $secondGroup;

        // Set GroupRepository expected return values for find
        $this->groupRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($firstGroup, $secondGroup) {
                switch ($arg) {
                    case 1: return $firstGroup;
                    case 2: return $secondGroup;
                    default: return null;
                }
            });
    }
}
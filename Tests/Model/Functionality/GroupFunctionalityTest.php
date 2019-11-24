<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.6.19
 * Time: 15:47
 */

namespace Tests\Model\Functionality;

use App\Model\Entity\Category;
use App\Model\Entity\Group;
use App\Model\Entity\SuperGroup;
use App\Model\Functionality\GroupFunctionality;
use App\Model\Repository\CategoryRepository;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\SuperGroupRepository;
use App\Model\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class GroupFunctionalityTest
 * @package App\AppTests\Model\Functionality
 */
class GroupFunctionalityTest extends FunctionalityTestCase
{
    /**
     * @var MockObject
     */
    protected $superGroupRepositoryMock;

    /**
     * @var MockObject
     */
    protected $categoryRepositoryMock;

    /**
     * @var MockObject
     */
    protected $userRepositoryMock;

    /**
     * @var Category
     */
    protected $firstCategory;

    /**
     * @var Category
     */
    protected $secondCategory;

    /**
     * @var SuperGroup
     */
    protected $superGroup;

    /**
     * @var SuperGroup
     */
    protected $superGroupNew;

    /**
     * @var Group
     */
    protected $group;

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        // Create first Category
        $firstCategory = new Category();
        $firstCategory->setId(1);
        $firstCategory->setLabel('TEST_FIRST_CATEGORY');
        $this->firstCategory = $firstCategory;

        // Create second Category
        $secondCategory = new Category();
        $secondCategory->setId(2);
        $secondCategory->setLabel('TEST_SECOND_CATEGORY');
        $this->secondCategory = $secondCategory;

        // Create first SuperGroup
        $superGroup = new SuperGroup();
        $superGroup->setId(1);
        $superGroup->setLabel('TEST_SUPER_GROUP');

        // Create default Group
        $group = new Group();
        $group->setId(1);
        $group->setLabel('TEST_GROUP_DEFAULT');
        $group->setSuperGroup($superGroup);
        $this->group = $group;

        // Finalize default Group
        $superGroup->setGroups(new ArrayCollection([$group]));
        $this->superGroup = $superGroup;

        // Create second SuperGroup
        $superGroupNew = new SuperGroup();
        $superGroupNew->setId(2);
        $superGroupNew->setLabel('TEST_SUPER_GROUP_NEW');
        $this->superGroupNew = $superGroupNew;

        // Mock the GroupRepository
        $this->repositoryMock = $this->getMockBuilder(GroupRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set repository expected return values for find
        $this->repositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($group) {
                $map = [
                    1 => $group,
                    50 => null
                ];
                return $map[$arg];
            });

        // Mock the SuperGroupRepository
        $this->superGroupRepositoryMock = $this->getMockBuilder(SuperGroupRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set SuperGroupRepository expected return values for find
        $this->superGroupRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($superGroup, $superGroupNew) {
                $map = [
                    1 => $superGroup,
                    2 => $superGroupNew,
                    50 => null
                ];
                return $map[$arg];
            });

        // Mock the CategoryRepository
        $this->categoryRepositoryMock = $this->getMockBuilder(CategoryRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->categoryRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($firstCategory, $secondCategory) {
                $map = [
                    1 => $firstCategory,
                    2 => $secondCategory,
                    50 => null
                ];
                return $map[$arg];
            });

        // Mock the UserRepository
        $this->userRepositoryMock = $this->getMockBuilder(UserRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Instantiate tested class
        $this->functionality = new GroupFunctionality(
            $this->em, $this->repositoryMock, $this->superGroupRepositoryMock, $this->categoryRepositoryMock,
            $this->userRepositoryMock
        );
    }

    /**
     * @throws \Exception
     */
    public function testFunctionality(): void
    {
        // Data for Group create
        $data = ArrayHash::from([
            'label' => 'TEST_GROUP',
            'superGroup' => 1,
            'created' => new DateTime('2000-01-01')
        ]);

        // Prepare expected data
        $groupExpected = new Group();
        $groupExpected->setLabel($data->label);
        $groupExpected->setSuperGroup($this->superGroupRepositoryMock->find(1));
        $groupExpected->setCreated(new DateTime('2000-01-01'));

        // Create group and test expected data
        $group = $this->functionality->create($data);
        $this->assertEquals($groupExpected, $group);

        // Set repository expected return values for find
        $this->repositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($group) {
                $map = [
                    1 => $group,
                    50 => null
                ];
                return $map[$arg];
            });

        // Data for Group update
        $data = ArrayHash::from([
            'label' => 'TEST_GROUP_NEW',
            'superGroup' => 2
        ]);

        // Update group and test expected data
        $group = $this->functionality->update(1, $data);
        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals($data->label, $group->getLabel());
        $this->assertEquals($this->superGroupRepositoryMock->find($data->superGroup), $group->getSuperGroup());

        // Try to delete, success expected
        $this->assertTrue($this->functionality->delete(1));

        // Try to delete, exception expected
        $this->expectException(EntityNotFoundException::class);
        $this->functionality->delete(50);
    }

    public function testUpdatePermissions(): void
    {
        // Get entities
        $group = $this->group;
        $firstCategory = $this->firstCategory;
        $secondCategory = $this->secondCategory;

        // Update Group permissions and test expected values
        $this->functionality->updatePermissions(1, [1, 2]);
        $group = $this->repositoryMock->find(1);
        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals(2, $group->getCategories()->count());
        $this->assertEquals([1, 2], $group->getCategoriesId());
        $this->assertEquals(new ArrayCollection([$firstCategory, $secondCategory]), $group->getCategories());
    }
}
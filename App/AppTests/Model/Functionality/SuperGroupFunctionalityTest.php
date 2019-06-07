<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.6.19
 * Time: 12:57
 */

namespace App\AppTests\Model\Functionality;

use App\Model\Entity\Category;
use App\Model\Entity\Group;
use App\Model\Entity\SuperGroup;
use App\Model\Functionality\GroupFunctionality;
use App\Model\Functionality\SuperGroupFunctionality;
use App\Model\Repository\CategoryRepository;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\SuperGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class SuperGroupTest
 * @package App\AppTests\Model\Functionality
 */
class SuperGroupFunctionalityTest extends FunctionalityTestCase
{
    /**
     * @var MockObject
     */
    protected $categoryRepositoryMock;

    /**
     * @var MockObject
     */
    protected $groupRepositoryMock;

    /**
     * @var GroupFunctionality
     */
    protected $groupFunctionality;

    /**
     * @var Category
     */
    protected $firstCategory;

    /**
     * @var Category
     */
    protected $secondCategory;

    /**
     * @var Group
     */
    protected $firstGroup;

    /**
     * @var Group
     */
    protected $secondGroup;

    /**
     * @var SuperGroup
     */
    protected $superGroup;

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

        // Create default SuperGroup
        $superGroup = new SuperGroup();
        $superGroup->setId(1);
        $superGroup->setLabel('TEST_SUPER_GROUP_DEFAULT');

        // Create first Group
        $firstGroup = new Group();
        $firstGroup->setId(1);
        $firstGroup->setLabel('TEST_FIRST_GROUP');
        $firstGroup->setSuperGroup($superGroup);
        $this->firstGroup = $firstGroup;

        // Create second Group
        $secondGroup = new Group();
        $secondGroup->setId(2);
        $secondGroup->setLabel('TEST_SECOND_GROUP');
        $secondGroup->setSuperGroup($superGroup);
        $this->secondGroup = $secondGroup;

        // Finalize default Group
        $superGroup->setGroups(new ArrayCollection([$firstGroup, $secondGroup]));
        $this->superGroup = $superGroup;

        // Mock the SuperGroupRepository
        $this->repositoryMock = $this->getMockBuilder(SuperGroupRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set repository expected return values for find
        $this->repositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($superGroup) {
                $map = [
                    1 => $superGroup,
                    50 => null
                ];
                return $map[$arg];
            });

        // Mock the CategoryRepository
        $this->categoryRepositoryMock = $this->getMockBuilder(CategoryRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set CategoryRepository expected return values for find
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

        // Mock the GroupRepository
        $this->groupRepositoryMock = $this->getMockBuilder(GroupRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set GroupRepository expected return values for find
        $this->groupRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($firstGroup, $secondGroup) {
                $map = [
                    1 => $firstGroup,
                    2 => $secondGroup,
                    50 => null
                ];
                return $map[$arg];
            });

        // Instantiate GroupFunctionality
        $this->groupFunctionality = new GroupFunctionality($this->em, $this->groupRepositoryMock, $this->repositoryMock, $this->categoryRepositoryMock);

        // Instantiate tested class
        $this->functionality = new SuperGroupFunctionality($this->em, $this->repositoryMock, $this->categoryRepositoryMock, $this->groupFunctionality);
    }

    /**
     * @throws \Exception
     */
    public function testFunctionality(): void
    {
        // Data for SuperGroupCreate
        $data = ArrayHash::from([
            'label' => 'TEST_SUPER_GROUP'
        ]);

        // Create SuperGroup and test expected data
        $superGroup = $this->functionality->create($data);
        $this->assertInstanceOf(SuperGroup::class, $superGroup);
        $this->assertEquals($data->label, $superGroup->getLabel());

        // Set repository expected return values for find
        $this->repositoryMock->expects($this->atLeastOnce())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($superGroup) {
                $map = [
                    1 => $superGroup,
                    50 => null
                ];
                return $map[$arg];
            });

        // Data for SuperGroup update
        $data = ArrayHash::from([
            'label' => 'NEW_TEST_SUPER_GROUP'
        ]);

        // Update SuperGroup and test expected data
        $superGroup = $this->functionality->update(1, $data);
        $this->assertInstanceOf(SuperGroup::class, $superGroup);
        $this->assertEquals($data->label, $superGroup->getLabel());

        // Try to delete, success expected
        $this->assertEquals(true, $this->functionality->delete(1));

        // Try to delete, exception expected
        $this->expectException(EntityNotFoundException::class);
        $this->functionality->delete(50);
    }

    public function testUpdatePermissions(): void
    {
        // Get Groups
        $firstGroup = $this->firstGroup;
        $secondGroup = $this->secondGroup;

        // Get Categories
        $firstCategory = $this->firstCategory;
        $secondCategory = $this->secondCategory;

        // Update SuperGroup permissions
        $this->functionality->updatePermissions(1, [1,2]);

        // Test SuperGroup expected values
        $superGroup = $this->repositoryMock->find(1);
        $this->assertInstanceOf(SuperGroup::class, $superGroup);
        $this->assertEquals(2, $superGroup->getCategories()->count());
        $this->assertEquals([1, 2], $superGroup->getCategoriesId());
        $this->assertEquals(new ArrayCollection([$firstCategory, $secondCategory]), $superGroup->getCategories());

        // Test firstGroup expected values
        $this->assertEquals(new ArrayCollection([$firstCategory, $secondCategory]), $firstGroup->getCategories());
        $this->assertEquals([1, 2], $firstGroup->getCategoriesId());

        // Test secondGroup expected values
        $this->assertEquals(new ArrayCollection([$firstCategory, $secondCategory]), $secondGroup->getCategories());
        $this->assertEquals([1, 2], $secondGroup->getCategoriesId());
    }
}
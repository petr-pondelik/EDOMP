<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.6.19
 * Time: 17:55
 */

namespace Tests\Model\Functionality;

use App\Model\Entity\Category;
use App\Model\Entity\SubCategory;
use App\Model\Functionality\SubCategoryFunctionality;
use App\Model\Repository\CategoryRepository;
use App\Model\Repository\SubCategoryRepository;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class SubCategoryFunctionalityTest
 * @package App\AppTests\Model\Functionality
 */
class SubCategoryFunctionalityTest extends FunctionalityTestCase
{
    /**
     * @var MockObject
     */
    protected $categoryRepositoryMock;

    /**
     * @var Category
     */
    protected $category;

    /**
     * @var Category
     */
    protected $categoryNew;

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        // Create first Category
        $category = new Category();
        $category->setLabel('TEST_CATEGORY');
        $this->category = $category;

        // Create second Category
        $categoryNew = new Category();
        $categoryNew->setLabel('TEST_CATEGORY_NEW');
        $this->categoryNew = $categoryNew;

        // Mock the SubCategoryRepository
        $this->repositoryMock = $this->getMockBuilder(SubCategoryRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Mock the CategoryRepository
        $this->categoryRepositoryMock = $this->getMockBuilder(CategoryRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for CategoryRepository
        $this->categoryRepositoryMock->expects($this->atLeastOnce())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($category, $categoryNew) {
                $map = [
                    1 => $category,
                    2 => $categoryNew,
                    50 => null
                ];
                return $map[$arg];
            });

        // Instantiate tested class
        $this->functionality = new SubCategoryFunctionality($this->em, $this->repositoryMock, $this->categoryRepositoryMock);
    }

    public function testFunctionality(): void
    {
        // Data for SubCategory create
        $data = ArrayHash::from([
            'label' => 'TEST_SUB_CATEGORY',
            'category_id' => 1
        ]);

        // Create SubCategory and test expected data
        $subCategory = $this->functionality->create($data);
        $this->assertInstanceOf(SubCategory::class, $subCategory);
        $this->assertEquals('TEST_SUB_CATEGORY', $subCategory->getLabel());
        $this->assertInstanceOf(Category::class, $subCategory->getCategory());
        $this->assertEquals($this->category, $subCategory->getCategory());

        // Set expected return values for SubCategoryRepository
        $this->repositoryMock->expects($this->atLeastOnce())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($subCategory) {
                $map = [
                    1 => $subCategory,
                    50 => null
                ];
                return $map[$arg];
            });

        // Data for SubCategory update
        $data = ArrayHash::from([
            'label' => 'NEW_TEST_SUB_CATEGORY',
            'category' => 2
        ]);

        // Update SubCategory and test expected data
        $subCategory = $this->functionality->update(1, $data);
        $this->assertInstanceOf(SubCategory::class, $subCategory);
        $this->assertEquals($subCategory->getLabel(), 'NEW_TEST_SUB_CATEGORY');
        $this->assertInstanceOf(Category::class, $subCategory->getCategory());
        $this->assertEquals($this->categoryNew, $subCategory->getCategory());

        // Try to delete, success expected
        $this->assertTrue($this->functionality->delete(1));

        // Try to delete, exception expected
        $this->expectException(EntityNotFoundException::class);
        $this->functionality->delete(50);
    }
}
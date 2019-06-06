<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.6.19
 * Time: 17:55
 */

namespace App\AppTests\Model\Functionality;

use App\Model\Entity\Category;
use App\Model\Entity\SubCategory;
use App\Model\Functionality\SubCategoryFunctionality;
use App\Model\Repository\CategoryRepository;
use App\Model\Repository\SubCategoryRepository;
use Nette\Utils\ArrayHash;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class SubCategoryFunctionalityTest
 * @package App\AppTests\Model\Functionality
 */
class SubCategoryFunctionalityTest extends FunctionalityTestCase
{
    /**
     * @var Category
     */
    protected $category;

    /**
     * @var MockObject
     */
    protected $categoryRepositoryMock;

    /**
     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        parent::setUp();

        $category = new Category();
        $category->setLabel('TEST_CATEGORY');
        $this->category = $category;

        $this->repositoryMock = $this->getMockBuilder(SubCategoryRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->categoryRepositoryMock = $this->getMockBuilder(CategoryRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->categoryRepositoryMock->expects($this->atLeastOnce())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($category) {
                $map = [
                    1 => $category,
                    50 => null
                ];
                return $map[$arg];
            });

        $this->functionality = new SubCategoryFunctionality($this->em, $this->repositoryMock, $this->categoryRepositoryMock);
    }

    public function testFunctionality(): void
    {
        $data = ArrayHash::from([
            'label' => 'TEST_CATEGORY',
            'category_id' => 1
        ]);

        $subCategory = $this->functionality->create($data);
        $this->assertInstanceOf(SubCategory::class, $subCategory);
    }
}
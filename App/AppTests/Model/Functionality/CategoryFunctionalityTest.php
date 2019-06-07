<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 31.5.19
 * Time: 23:23
 */

declare(strict_types=1);

namespace App\AppTests\Model\Functionality;

use App\Model\Functionality\CategoryFunctionality;
use App\Model\Manager\ConstraintEntityManager;
use App\Model\Repository\CategoryRepository;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;
use App\Model\Entity\Category;

/**
 * Class CategoryFunctionalityCreateTest
 */
class CategoryFunctionalityTest extends FunctionalityTestCase
{
    /**
     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        parent::setUp();

        // Mock the CategoryRepository
        $this->repositoryMock = $this->getMockBuilder(CategoryRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Instantiate tested class
        $this->functionality = new CategoryFunctionality($this->em, $this->repositoryMock);
    }

    /**
     * @throws \Exception
     */
    public function testFunctionality(): void
    {
        // Data for Category create
        $data = ArrayHash::from([
            'label' => 'TEST_CATEGORY'
        ]);

        // Create category and test expected data
        $category = $this->functionality->create($data);
        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('TEST_CATEGORY', $category->getLabel());

        // Set repository expected return values for find
        $this->repositoryMock->expects($this->atLeastOnce())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($category) {
                $map = [
                    1 => $category,
                    50 => null
                ];
                return $map[$arg];
            });

        // Data for Category update
        $data = ArrayHash::from([
            'label' => 'NEW_TEST_CATEGORY'
        ]);

        // Update Category and test expected data
        $category = $this->functionality->update(1, $data);
        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals($category->getLabel(), 'NEW_TEST_CATEGORY');

        // Try to delete, success expected
        $this->assertEquals(true, $this->functionality->delete(1));

        // Try to delete, exception expected
        $this->expectException(EntityNotFoundException::class);
        $this->functionality->delete(50);
    }
}
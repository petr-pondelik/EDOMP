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
class CategoryFunctionalityCreateTest extends FunctionalityTestCase
{
    /**
     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repositoryMock = $this->getMockBuilder(CategoryRepository::class)
            ->setMethods(['findBy', 'find'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->functionality = new CategoryFunctionality($this->em, $this->repositoryMock);
    }

    /**
     * @throws \Exception
     */
    public function testFunctionality(): void
    {
        $data = ArrayHash::from([
            'label' => 'TEST_CATEGORY'
        ]);

        $category = $this->functionality->create($data);
        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('TEST_CATEGORY', $category->getLabel());

        $this->repositoryMock->expects($this->atLeastOnce())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($category) {
                $map = [
                    1 => $category,
                    50 => null
                ];
                return $map[$arg];
            });

        $data = ArrayHash::from([
            'label' => 'NEW_TEST_CATEGORY'
        ]);

        $this->assertEquals($this->repositoryMock->find(1), $category);

        $category = $this->functionality->update(1, $data);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals($category->getLabel(), 'NEW_TEST_CATEGORY');

        $this->functionality->delete(1);
        $this->expectException(EntityNotFoundException::class);
        $this->functionality->delete(50);
    }
}
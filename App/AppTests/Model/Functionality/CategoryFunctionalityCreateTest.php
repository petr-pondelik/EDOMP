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
use App\Services\ValidationService;
use Nette\Security\User;
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

        $em = $this->getMockBuilder(ConstraintEntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->functionality = new CategoryFunctionality($em, $this->repositoryMock);
    }

    /**
     * @throws \Exception
     */
    public function testFunctionality(): void
    {
        $data = ArrayHash::from([
            'label' => 'TEST_CATEGORY'
        ]);

        $category = new Category();
        $category->setLabel('TEST_CATEGORY');

        $this->repositoryMock->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($category);

        //$category = $this->repositoryMock->findBy(['id' => 1]);

        $data = ArrayHash::from([
            'label' => 'NEW_TEST_CATEGORY'
        ]);

        $category = $this->functionality->update(1, $data);

        $this->assertEquals($category->getLabel(), 'NEW_TEST_CATEGORY');


    }
}
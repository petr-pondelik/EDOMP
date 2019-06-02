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
use Nette\Utils\ArrayHash;
use App\Model\Entity\Category;


/**
 * Class CategoryFunctionalityCreateTest
 */
class CategoryFunctionalityCreateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $em;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $categoryRepository;

    /**
     * @var CategoryFunctionality
     */
    protected $categoryFunctionality;

    public function setUp(): void
    {
        parent::setUp();
        $this->em = $this->getMockBuilder(ConstraintEntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->categoryRepository = $this->getMockBuilder(CategoryRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->categoryFunctionality = new CategoryFunctionality($this->em, $this->categoryRepository);
    }

    /**
     * @throws \Exception
     */
    public function testInitialization()
    {
        $data = ArrayHash::from([
            "label" => "TESTCATEGORY1"
        ]);

        $this->assertEquals($this->categoryFunctionality->getTest(), 1);

        $category1 = new Category();
        $category1->setLabel("TESTCATEGORY1");


        $category2 = $this->categoryFunctionality->create($data);

        $this->assertInstanceOf(Category::class, $category2);

        $this->assertEquals($category1->getLabel(), $category2->getLabel());
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 31.5.19
 * Time: 23:23
 */

declare(strict_types=1);


use PHPUnit\Framework\TestCase;
use Nette\Utils\ArrayHash;
use App\Model\Functionality\CategoryFunctionality;
use App\Model\Entity\Category;
use App\Model\Manager\ConstraintEntityManager;


/**
 * Class CategoryFunctionalityCreateTest
 */
class CategoryFunctionalityCreateTest extends TestCase
{
    /**
     * @var CategoryFunctionality
     */
    protected $categoryFunctionality;

    public function testInitialization()
    {
        $data = ArrayHash::from([
            "label" => "TESTCATEGORY1"
        ]);

        $em = $this->getMockBuilder("App\Model\Manager\ConstraintEntityManager")
            ->disableOriginalConstructor()
            ->getMock();

        $categoryRepository = $this->getMockBuilder("App\Model\Repository\CategoryRepository")
            ->disableOriginalConstructor()
            ->getMock();

        $this->categoryFunctionality = new CategoryFunctionality($em, $categoryRepository);

        $this->assertEquals($this->categoryFunctionality->getTest(), 1);

        $this->assertInstanceOf(Category::class, $this->categoryFunctionality->create($data));
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 2.6.19
 * Time: 23:54
 */

namespace AppTests\Model\Entity;



use App\AppTests\Entity\EntityTestCase;
use App\Model\Entity\Category;
use App\Model\Entity\SubCategory;

/**
 * Class SubCategoryEntityTest
 * @package AppTests\Model\Entity
 */
class SubCategoryEntityTest extends EntityTestCase
{
    /**
     * @throws \Exception
     */
    public function testCreateSuccess()
    {
        $category = new Category();
        $category->setLabel("TESTCATEGORY");
        $subCategory = new SubCategory();
        $subCategory->setLabel("TESTSUBCATEGORY");
        $subCategory->setCategory($category);
        $this->assertInstanceOf(SubCategory::class, $subCategory);
        $errors = $this->validator->validate($subCategory);
        $this->assertEquals($errors->count(), 0);
    }
}
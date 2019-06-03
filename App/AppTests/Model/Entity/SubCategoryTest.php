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
class SubCategoryTest extends EntityTestCase
{
    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $category = new Category();
        $category->setLabel("TESTCATEGORY");
        $entity = new SubCategory();
        $entity->setLabel("TESTSUBCATEGORY");
        $entity->setCategory($category);
        $this->assertInstanceOf(SubCategory::class, $entity);
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 0);
    }

    /**
     * @throws \Exception
     */
    public function testCreateError(): void
    {
        $errorMsgs = [
            0 => "Category can't be blank.",
            1 => "Label can't be blank."
        ];

        $entity = new SubCategory();
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 2);

        foreach ($errors as $key => $error)
            $this->assertEquals($error->getMessage(), $errorMsgs[$key]);
    }
}
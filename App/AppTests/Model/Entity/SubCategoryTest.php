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
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class SubCategoryEntityTest
 * @package AppTests\Model\Entity
 */
class SubCategoryTest extends EntityTestCase
{
    /**
     * @var Category
     */
    protected $category;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $category = new Category();
        $category->setLabel('TEST_CATEGORY');
        $this->category = $category;
    }

    /**
     * @throws \Exception
     */
    public function testValues(): void
    {
        $entity = new SubCategory();
        $entity->setLabel('TEST_SUBCATEGORY');
        $entity->setCategory($this->category);

        $this->assertEquals($entity->getLabel(), 'TEST_SUBCATEGORY');
        $this->assertEquals($entity->getCategory(), $this->category);
        $this->assertEquals($entity->getProblems(), new ArrayCollection());
    }

    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $entity = new SubCategory();
        $entity->setLabel('TEST_SUBCATEGORY');
        $entity->setCategory($this->category);

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

        foreach ($errors as $key => $error){
            $this->assertEquals($error->getMessage(), $errorMsgs[$key]);
        }
    }
}
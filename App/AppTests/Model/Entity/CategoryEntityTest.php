<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 2.6.19
 * Time: 23:41
 */

namespace AppTests\Model\Entity;


use App\AppTests\Entity\EntityTestCase;
use App\Model\Entity\Category;

/**
 * Class CategoryEntityTest
 * @package AppTests\Model\Entity
 */
class CategoryEntityTest extends EntityTestCase
{
    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $entity = new Category();
        $entity->setLabel("TESTCATEGORY");
        $this->assertInstanceOf(Category::class, $entity);
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 0);
    }

    /**
     * @throws \Exception
     */
    public function testCreateError(): void
    {
        $entity = new Category();
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 1);
        $this->assertEquals($errors->get(0)->getMessage(), 'Label can\'t be blank.');
    }
}
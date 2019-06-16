<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 3.6.19
 * Time: 23:13
 */

namespace Tests\Model\Entity;


use App\Model\Entity\SuperGroup;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class SuperGroupTest
 * @package AppTests\Model\Entity
 */
class SuperGroupTest extends EntityTestCase
{
    /**
     * @throws \Exception
     */
    public function testValues(): void
    {
        $entity = new SuperGroup();
        $entity->setLabel('TEST_SUPER_GROUP');

        $this->assertEquals($entity->getLabel(), 'TEST_SUPER_GROUP');
        $this->assertEquals($entity->getGroups(), new ArrayCollection());
        $this->assertEquals($entity->getCategories(), new ArrayCollection());
        $this->assertEquals($entity->getCategoriesId(), []);
        $this->assertEquals($entity->getCreatedBy(), null);
    }

    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $entity = new SuperGroup();
        $entity->setLabel('TEST_SUPER_GROUP');

        $this->assertInstanceOf(SuperGroup::class, $entity);
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 0);
    }

    /**
     * @throws \Exception
     */
    public function testCreateError(): void
    {
        $entity = new SuperGroup();
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 1);
        $this->assertEquals($errors->get(0)->getMessage(), 'Label can\'t be blank.');
    }
}
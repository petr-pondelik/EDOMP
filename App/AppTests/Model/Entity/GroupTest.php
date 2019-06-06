<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 3.6.19
 * Time: 23:15
 */

namespace AppTests\Model\Entity;


use App\AppTests\Entity\EntityTestCase;
use App\Model\Entity\Group;
use App\Model\Entity\SuperGroup;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class GroupTest
 * @package AppTests\Model\Entity
 */
class GroupTest extends EntityTestCase
{
    /**
     * @var SuperGroup
     */
    protected $superGroup;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $superGroup = new SuperGroup();
        $superGroup->setLabel('TEST_SUPER_GROUP');
        $this->superGroup = $superGroup;
    }

    /**
     * @throws \Exception
     */
    public function testValues(): void
    {
        $entity = new Group();
        $entity->setLabel('TEST_GROUP');
        $entity->setSuperGroup($this->superGroup);

        $this->assertEquals($entity->getLabel(), 'TEST_GROUP');
        $this->assertEquals($entity->getSuperGroup(), $this->superGroup);
        $this->assertEquals($entity->getCategories(), new ArrayCollection());
        $this->assertEquals($entity->getCategoriesId(), []);
    }

    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $entity = new Group();
        $entity->setLabel('TEST_GROUP');
        $entity->setSuperGroup($this->superGroup);

        $this->assertInstanceOf(Group::class, $entity);
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 0);
    }

    /**
     * @throws \Exception
     */
    public function testCreateError(): void
    {
        $errorMsgs = [
            0 => "SuperGroup can't be blank.",
            1 => "Label can't be blank."
        ];

        $entity = new Group();
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 2);

        foreach ($errors as $key => $error){
            $this->assertEquals($errors->get($key)->getMessage(), $errorMsgs[$key]);
        }
    }
}
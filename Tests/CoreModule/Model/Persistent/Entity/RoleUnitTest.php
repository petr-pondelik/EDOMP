<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.11.19
 * Time: 0:06
 */

namespace App\Tests\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Entity\Role;

/**
 * Class RoleUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
class RoleUnitTest extends PersistentEntityTestCase
{
    /**
     * @var array
     */
    protected $errorMessages = [
        0 => "Key can't be blank.",
        1 => "Label can't be blank.",
    ];

    public function testValidState(): void
    {
        $entity = new Role();
        $label = 'TEST_LABEL';
        $key = 'TEST_KEY';

        $entity->setLabel($label);
        $entity->setKey($key);

        $this->assertInstanceOf(Role::class, $entity);
        $this->assertEquals($label, $entity->getLabel());
        $this->assertEquals($key, $entity->getKey());

        $this->assertValidByValidator($entity);
    }

    public function testInvalidState(): void
    {
        $entity = new Role();
        $this->assertValidatorViolations($entity);
    }
}
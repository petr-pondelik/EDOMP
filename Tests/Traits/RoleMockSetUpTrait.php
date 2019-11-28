<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.11.19
 * Time: 18:13
 */

namespace App\Tests\Traits;

use App\CoreModule\Model\Persistent\Entity\Role;

/**
 * Trait RoleMockSetUpTrait
 * @package App\Tests\Traits
 */
trait RoleMockSetUpTrait
{
    /**
     * @var Role
     */
    protected $roleMock;

    protected function setUpRoleMock(): void
    {
        $this->roleMock = $this->getMockBuilder(Role::class)->disableOriginalConstructor()->getMock();
    }
}
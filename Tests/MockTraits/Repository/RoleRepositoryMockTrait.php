<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.12.19
 * Time: 0:03
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\Role;
use App\CoreModule\Model\Persistent\Repository\RoleRepository;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait RoleRepositoryMockTrait
 * @package App\Tests\MockTraits\Repository
 */
trait RoleRepositoryMockTrait
{
    /**
     * @var MockObject
     */
    protected $roleRepositoryMock;

    /**
     * @var Role
     */
    protected $firstRole;

    /**
     * @var Role
     */
    protected $secondRole;

    /**
     * @throws \Exception
     */
    protected function setUpRoleRepositoryMock(): void
    {
        $this->roleRepositoryMock = $this->getMockBuilder(RoleRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first Role
        $firstRole = new Role();
        $firstRole->setId(1);
        $firstRole->setLabel('TEST_FIRST_ROLE');
        $firstRole->setKey('testFirstRole');
        $firstRole->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->firstRole = $firstRole;

        // Create second Role
        $secondRole = new Role();
        $secondRole->setId(2);
        $secondRole->setLabel('TEST_SECOND_ROLE');
        $secondRole->setKey('testSecondRole');
        $secondRole->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->secondRole = $secondRole;

        // Set RoleRepository expected return values for find
        $this->roleRepositoryMock->method('find')
            ->willReturnCallback(static function ($arg) use ($firstRole, $secondRole) {
                switch ($arg) {
                    case 1: return $firstRole;
                    case 2: return $secondRole;
                    default: return null;
                }
            });
    }
}
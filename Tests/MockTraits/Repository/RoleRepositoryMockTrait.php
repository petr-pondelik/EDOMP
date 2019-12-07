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
     * @var Role
     */
    protected $thirdRole;

    /**
     * @throws \Exception
     */
    protected function setUpRoleRepositoryMock(): void
    {
        $this->roleRepositoryMock = $this->getMockBuilder(RoleRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Create first Role
        $firstRole = new Role();
        $firstRole->setId(1);
        $firstRole->setLabel('Administrátor');
        $firstRole->setKey('admin');
        $firstRole->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->firstRole = $firstRole;

        // Create second Role
        $secondRole = new Role();
        $secondRole->setId(2);
        $secondRole->setLabel('Učitel');
        $secondRole->setKey('teacher');
        $secondRole->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->secondRole = $secondRole;

        // Create third Role
        $thirdRole = new Role();
        $thirdRole->setId(3);
        $thirdRole->setLabel('Student');
        $thirdRole->setKey('student');
        $thirdRole->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->thirdRole = $thirdRole;

        // Set RoleRepository expected return values for find
        $this->roleRepositoryMock->method('find')
            ->willReturnCallback(static function ($arg) use ($firstRole, $secondRole, $thirdRole) {
                switch ($arg) {
                    case 1: return $firstRole;
                    case 2: return $secondRole;
                    case 3: return $thirdRole;
                    default: return null;
                }
            });
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.11.19
 * Time: 19:52
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\Role;
use App\CoreModule\Model\Persistent\Repository\RoleRepository;
use Nette\Utils\DateTime;

/**
 * Class RoleRepositoryIntegrationTest
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
final class RoleRepositoryIntegrationTest extends RepositoryIntegrationTestCase
{
    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->roleRepository = $this->container->getByType(RoleRepository::class);
    }

    /**
     * @throws \Exception
     */
    public function testFind(): void
    {
        $adminRole = new Role();
        $adminRole->setId(1);
        $adminRole->setKey('admin');
        $adminRole->setLabel('Administrátor');
        $adminRole->setCreated(DateTime::from('2019-05-01 20:39:35'));

        $teacherRole = new Role();
        $teacherRole->setId(2);
        $teacherRole->setKey('teacher');
        $teacherRole->setLabel('Učitel');
        $teacherRole->setCreated(DateTime::from('2019-05-01 20:39:51'));

        $studentRole = new Role();
        $studentRole->setId(3);
        $studentRole->setKey('student');
        $studentRole->setLabel('Student');
        $studentRole->setCreated(DateTime::from('2019-05-01 20:39:51'));

        $expected = [$adminRole, $teacherRole, $studentRole];
        $found = $this->roleRepository->findAll();

        $this->assertCount(3, $found);
        $this->assertEquals($expected, $found);
        $this->assertEquals(4, $this->roleRepository->getSequenceVal());
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->roleRepository = null;
    }
}
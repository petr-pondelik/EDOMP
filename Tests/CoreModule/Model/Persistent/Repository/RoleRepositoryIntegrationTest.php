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
use Nette\Security\User;
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
    protected $repository;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Role
     */
    protected $adminRole;

    /**
     * @var Role
     */
    protected $teacherRole;

    /**
     * @var Role
     */
    protected $studentRole;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->container->getByType(RoleRepository::class);
        $this->user = $this->container->getByType(User::class);

        $adminRole = new Role();
        $adminRole->setId(1);
        $adminRole->setKey('admin');
        $adminRole->setLabel('Administrátor');
        $adminRole->setCreated(DateTime::from('2019-05-01 20:39:35'));
        $this->adminRole = $adminRole;

        $teacherRole = new Role();
        $teacherRole->setId(2);
        $teacherRole->setKey('teacher');
        $teacherRole->setLabel('Učitel');
        $teacherRole->setCreated(DateTime::from('2019-05-01 20:39:51'));
        $this->teacherRole = $teacherRole;

        $studentRole = new Role();
        $studentRole->setId(3);
        $studentRole->setKey('student');
        $studentRole->setLabel('Student');
        $studentRole->setCreated(DateTime::from('2019-05-01 20:39:51'));
        $this->studentRole = $studentRole;
    }

    /**
     * @throws \Exception
     */
    public function testFindAll(): void
    {
        $expected = [$this->adminRole, $this->teacherRole, $this->studentRole];
        $found = $this->repository->findAll();

        $this->assertCount(3, $found);
        $this->assertEquals($expected, $found);
        $this->assertEquals(4, $this->repository->getSequenceVal());
    }

    /**
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \Nette\Security\AuthenticationException
     * @throws \Exception
     */
    public function testAdminFindAllowed(): void
    {
        $expected[3] = $this->studentRole;

        $this->user->login('jkohneke0@nba.com', '12345678');
        $found = $this->repository->findAllowed($this->user);

        $this->assertCount(1, $found);
        $this->assertEquals($expected, $found);

        $this->user->logout(true);
    }

    /**
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \Nette\Security\AuthenticationException
     */
    public function testTeacherFindAllowed(): void
    {
        $expected = [
            2 => $this->teacherRole,
            3 => $this->studentRole
        ];

        $this->user->login('admin', '12345678');
        $found = $this->repository->findAllowed($this->user);

        $this->assertCount(2, $found);
        $this->assertEquals($expected, $found);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->adminRole = null;
        $this->teacherRole = null;
        $this->studentRole = null;
        $this->user->logout(true);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.11.19
 * Time: 16:55
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\Group;
use App\CoreModule\Model\Persistent\Repository\GroupRepository;

/**
 * Class GroupRepositoryIntegrationTest
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
final class GroupRepositoryIntegrationTest extends SecuredRepositoryTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->getByType(GroupRepository::class);
    }

    public function testFindAll(): void
    {
        $labels = [ 'Administrators', 'Učitelé', '1.A', '2.B', '2.A', 'Odpolední skupina' ];
        $found = $this->repository->findAll();
        $this->assertCount(6, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(Group::class, $item);
            $this->assertEquals($labels[$key], (string) $item);
        }
    }

    /**
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \Nette\Security\AuthenticationException
     */
    public function testFindAllowed(): void
    {
        $this->user->login('admin', '12345678');
        $labels = [ 2 => 'Učitelé', 3 =>  '1.A', 4 => '2.B', 5 => '2.A', 6 => 'Odpolední skupina' ];

        /**
         * @var Group[] $found
         */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(5, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(Group::class, $item);
            $this->assertEquals($labels[$key], (string) $item);
        }

        $this->user->logout(true);

        $this->user->login('jkohneke0@nba.com', '12345678');
        $labels = [ 3 =>  '1.A', 4 => '2.B', 5 => '2.A'];

        /**
         * @var Group[] $found
         */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(3, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(Group::class, $item);
            $this->assertEquals($labels[$key], (string) $item);
        }

        $this->user->logout(true);
    }
}
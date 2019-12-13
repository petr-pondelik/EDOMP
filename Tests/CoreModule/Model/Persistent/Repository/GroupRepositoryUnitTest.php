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
 * Class GroupRepositoryUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
final class GroupRepositoryUnitTest extends SecuredRepositoryTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->getByType(GroupRepository::class);
    }

    public function testFind(): void
    {
        $labels = [
            'Administrators', 'Učitelé',
            '1.A', '1.B', '1.C',
            '2.A', '2.B', '2.C',
            'Páteční skupina', 'Sobotní skupina', 'Nedělní skupina'
        ];
        $found = $this->repository->findAll();
        $this->assertCount(11, $found);
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
        $this->user->login('admin', '12345678', true);
        $labels = [
            2 => 'Učitelé',
            3 =>  '1.A', 4 => '1.B', 5 => '1.C',
            6 => '2.A', 7 => '2.B', 8 => '2.C',
            9 => 'Páteční skupina', 10 => 'Sobotní skupina', 11 => 'Nedělní skupina'
        ];

        /**
         * @var Group[] $found
         */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(10, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(Group::class, $item);
            $this->assertEquals($labels[$key], (string) $item);
        }

        $this->user->logout(true);

        $this->user->login('jkohneke0@nba.com', '12345678', true);
        $labels = [ 3 =>  '1.A', 4 => '1.B', 5 => '1.C'];

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
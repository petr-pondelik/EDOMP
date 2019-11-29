<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.11.19
 * Time: 16:56
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\SuperGroup;
use App\CoreModule\Model\Persistent\Repository\SuperGroupRepository;

/**
 * Class SuperGroupRepositoryIntegrationTest
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
class SuperGroupRepositoryIntegrationTest extends SecuredRepositoryTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->getByType(SuperGroupRepository::class);
    }

    public function testFindAll(): void
    {
        $labels = [ 'Administrators', 'Učitelé', 'Střední škola', 'Externisté' ];
        $found = $this->repository->findAll();
        $this->assertCount(4, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(SuperGroup::class, $item);
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
        $labels = [ 3 => 'Střední škola', 4 => 'Externisté' ];

        /**
         * @var SuperGroup[] $found
         */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(2, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(SuperGroup::class, $item);
            $this->assertEquals($labels[$key], (string) $item);
        }

        $this->user->logout(true);

        $this->user->login('jkohneke0@nba.com', '12345678');
        $labels = [ 3 => 'Střední škola' ];

        /**
         * @var SuperGroup[] $found
         */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(1, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(SuperGroup::class, $item);
            $this->assertEquals($labels[$key], (string) $item);
        }

        $this->user->logout(true);
    }
}
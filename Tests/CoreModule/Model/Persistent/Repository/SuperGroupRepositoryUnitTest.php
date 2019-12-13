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
 * Class SuperGroupRepositoryUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
class SuperGroupRepositoryUnitTest extends SecuredRepositoryTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->getByType(SuperGroupRepository::class);
    }

    public function testFind(): void
    {
        $labels = [ 'Administrators', 'Učitelé', 'Střední škola', 'Střední', 'Externisté' ];
        $found = $this->repository->findAll();
        $this->assertCount(count($labels), $found);
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
        $this->user->login('admin', '12345678', true);
        $labels = [ 3 => 'Střední škola', 4 => 'Střední', 5 => 'Externisté' ];

        /**
         * @var SuperGroup[] $found
         */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(count($labels), $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(SuperGroup::class, $item);
            $this->assertEquals($labels[$key], (string) $item);
        }

        $this->user->logout(true);

        $this->user->login('jkohneke0@nba.com', '12345678', true);
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
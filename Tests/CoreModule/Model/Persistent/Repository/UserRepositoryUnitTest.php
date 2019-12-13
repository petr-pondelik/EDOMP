<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.11.19
 * Time: 21:01
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\User;
use App\CoreModule\Model\Persistent\Repository\UserRepository;

/**
 * Class UserRepositoryUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
final class UserRepositoryUnitTest extends SecuredRepositoryTestCase
{
    /**
     * @var UserRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->getByType(UserRepository::class);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testFind(): void
    {
        $found = $this->repository->findForAuthentication('admin', [1, 2]);
        $this->assertInstanceOf(User::class, $found);
        $this->assertEquals('admin', (string) $found);

        $found = $this->repository->findForAuthentication('jkohneke0@nba.com', [1, 2]);
        $this->assertInstanceOf(User::class, $found);
        $this->assertEquals('jkohneke0@nba.com', (string) $found);

        $found = $this->repository->findForAuthentication('awenman8@ucoz.com', [1, 2]);
        $this->assertNull($found);

        $found = $this->repository->findForAuthentication('awenman8@ucoz.com');
        $this->assertInstanceOf(User::class, $found);
        $this->assertEquals('awenman8@ucoz.com', (string) $found);
    }

    /**
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \Nette\Security\AuthenticationException
     */
    public function testFindAllowed(): void
    {
        $this->user->login('admin', '12345678', true);
        /** @var User[] $found */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(51, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(User::class, $item);
        }
        $this->user->logout(true);

        $this->user->login('jkohneke0@nba.com', '12345678', true);
        /** User[] $found */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(16, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(User::class, $item);
        }
        $this->user->logout(true);
    }
}
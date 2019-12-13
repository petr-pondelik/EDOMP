<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.11.19
 * Time: 21:02
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\Test;
use App\CoreModule\Model\Persistent\Repository\TestRepository;

/**
 * Class TestRepository
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
final class TestRepositoryUnitTest extends SecuredRepositoryTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->getByType(TestRepository::class);
    }

    public function testFind(): void
    {
        /** @var Test $found */
        $found = $this->repository->find(1);
        $this->assertInstanceOf(Test::class, $found);
        $this->assertEquals(1, $found->getId());
        $this->assertEquals('1. pol.', $found->getTerm());
        $this->assertEquals(1, $found->getTestNumber());
        $this->assertEquals(5, $found->getVariantsCnt());
    }

    /**
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \Nette\Security\AuthenticationException
     */
    public function testFindAllowed(): void
    {
        $this->user->login('admin', '12345678', true);
        /** @var Test[] $found */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(6, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(Test::class, $item);
        }
        $this->user->logout(true);

        $this->user->login('jkohneke0@nba.com', '12345678', true);
        /** Test[] $found */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(2, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(Test::class, $item);
        }
        $this->user->logout(true);

        $this->user->login('mhazzard1@wiley.com', '12345678', true);
        /** Test[] $found */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(2, $found);
        $this->user->logout(true);
    }
}
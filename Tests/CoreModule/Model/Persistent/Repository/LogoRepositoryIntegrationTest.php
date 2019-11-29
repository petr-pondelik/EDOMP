<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.11.19
 * Time: 21:07
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\Logo;
use App\CoreModule\Model\Persistent\Repository\LogoRepository;
use Nette\Security\User;

/**
 * Class DifficultyRepositoryIntegrationTest
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
final class LogoRepositoryIntegrationTest extends RepositoryIntegrationTestCase
{
    /**
     * @var LogoRepository
     */
    protected $logoRepository;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var array
     */
    protected $labels = [ 'Testing logo 1', 'Testing logo 2' ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->logoRepository = $this->container->getByType(LogoRepository::class);
        $this->user = $this->container->getByType(User::class);
    }

    public function testFindAll(): void
    {
        /**
         * @var Logo[] $found
         */
        $found = $this->logoRepository->findAll();
        $this->assertCount(2, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(Logo::class, $item);
            $this->assertEquals($this->labels[$key], $item->getLabel());
        }
    }

    /**
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \Nette\Security\AuthenticationException
     */
    public function testAdminFindAllowed(): void
    {
        $this->user->login('admin', '12345678');

        $labels = [ 1 => $this->labels[0], 2 => $this->labels[1] ];

        /**
         * @var Logo[] $found
         */
        $found = $this->logoRepository->findAllowed($this->user);
        $this->assertCount(2, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(Logo::class, $item);
            $this->assertEquals($labels[$key], $item->getLabel());
        }

        $this->user->logout(true);
    }

    public function testTeacherFindAllowed(): void
    {
        $this->user->login('jkohneke0@nba.com', '12345678');

        $labels = [ 1 => $this->labels[0] ];

        /**
        * @var Logo[] $found
        */
        $found = $this->logoRepository->findAllowed($this->user);
        $this->assertCount(1, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(Logo::class, $item);
            $this->assertEquals($labels[$key], $item->getLabel());
        }

        $this->user->logout(true);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->user->logout(true);
        $this->logoRepository = null;
        $this->user = null;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.11.19
 * Time: 18:17
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\Theme;
use App\CoreModule\Model\Persistent\Repository\ThemeRepository;

/**
 * Class ThemeRepositoryUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
final class ThemeRepositoryUnitTest extends SecuredRepositoryTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->getByType(ThemeRepository::class);
    }

    public function testFind(): void
    {
        $labels = [
            '1. Rovnice', '2. Posloupnosti',
            '1: Rovnice', '2: Posloupnosti',
            '1. Začátečníci', '2. Pokročilí'
        ];
        $found = $this->repository->findAll();
        $this->assertCount(count($labels), $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(Theme::class, $item);
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
            1 => '1. Rovnice', 2 => '2. Posloupnosti',
            3 => '1: Rovnice', 4 => '2: Posloupnosti',
            5 => '1. Začátečníci', 6 => '2. Pokročilí'
        ];

        /**
         * @var Theme[] $found
         */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(count($labels), $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(Theme::class, $item);
            $this->assertEquals($labels[$key], (string) $item);
        }

        $this->user->logout(true);

        $this->user->login('jkohneke0@nba.com', '12345678', true);
        $labels = [ 1 => '1. Rovnice', 2 => '2. Posloupnosti'];

        /**
         * @var Theme[] $found
         */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(2, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(Theme::class, $item);
            $this->assertEquals($labels[$key], (string) $item);
        }

        $this->user->logout(true);
    }
}
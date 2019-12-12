<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.11.19
 * Time: 18:25
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\SubTheme;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;

/**
 * Class SubThemeRepositoryUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
final class SubThemeRepositoryUnitTest extends SecuredRepositoryTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->getByType(SubThemeRepository::class);
    }

    public function testFind(): void
    {
        $labels = [
            '1.1. Lineární rovnice', '1.2. Kvadratické rovnice', '2.1. Aritmetické posloupnosti', '2.2. Geometrické posloupnosti',
            '1.1: Lineární rovnice', '1.2: Kvadratické rovnice', '2.1: Aritmetické posloupnosti', '2.2: Geometrické posloupnosti',
            '1.1. První lekce', '1.2. Druhá lekce', '2.1. První lekce', '2.2. Druhá lekce'
        ];
        $found = $this->repository->findAll();
        $this->assertCount(12, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(SubTheme::class, $item);
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
        $labels = [
            1 => '1.1. Lineární rovnice', 2 => '1.2. Kvadratické rovnice', 3 => '2.1. Aritmetické posloupnosti', 4 => '2.2. Geometrické posloupnosti',
            5 => '1.1: Lineární rovnice', 6 => '1.2: Kvadratické rovnice', 7 => '2.1: Aritmetické posloupnosti', 8 => '2.2: Geometrické posloupnosti',
            9 => '1.1. První lekce', 10 => '1.2. Druhá lekce', 11 => '2.1. První lekce', 12 => '2.2. Druhá lekce'
        ];

        /**
         * @var SubTheme[] $found
         */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(12, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(SubTheme::class, $item);
            $this->assertEquals($labels[$key], (string) $item);
        }

        $this->user->logout(true);

        $this->user->login('jkohneke0@nba.com', '12345678');
        $labels = [ 1 => '1.1. Lineární rovnice', 2 => '1.2. Kvadratické rovnice', 3 => '2.1. Aritmetické posloupnosti', 4 => '2.2. Geometrické posloupnosti'];

        /**
         * @var SubTheme[] $found
         */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(4, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(SubTheme::class, $item);
            $this->assertEquals($labels[$key], (string) $item);
        }

        $this->user->logout(true);
    }
}
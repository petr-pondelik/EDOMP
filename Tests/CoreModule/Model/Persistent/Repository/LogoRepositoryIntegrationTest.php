<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.11.19
 * Time: 21:07
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Repository\LogoRepository;

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

    protected function setUp(): void
    {
        parent::setUp();
        $this->logoRepository = $this->container->getByType(LogoRepository::class);
    }

    public function testFind(): void
    {
        $logos = $this->logoRepository->findAll();
        $this->assertCount(1, $logos);
    }
}
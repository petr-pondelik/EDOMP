<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.11.19
 * Time: 21:22
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;

/**
 * Class DifficultyRepositoryIntegrationTest
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
final class DifficultyRepositoryIntegrationTest extends RepositoryIntegrationTestCase
{
    /**
     * @var DifficultyRepository
     */
    protected $difficultyRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->difficultyRepository = $this->container->getByType(DifficultyRepository::class);
    }

    public function testFind(): void
    {
        $found = $this->difficultyRepository->findAll();
        $this->assertCount(3, $found);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.11.19
 * Time: 19:49
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;


use App\CoreModule\Model\Persistent\Repository\BaseRepository;
use App\Tests\EDOMPIntegrationTestCase;

/**
 * Class RepositoryIntegrationTestCase
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
abstract class RepositoryIntegrationTestCase extends EDOMPIntegrationTestCase
{
    /**
     * @var BaseRepository
     */
    protected $repository;

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->repository = null;
    }

    abstract public function testFindAll(): void;
}
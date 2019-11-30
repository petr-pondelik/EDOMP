<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.11.19
 * Time: 19:49
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;


use App\CoreModule\Model\Persistent\Repository\BaseRepository;
use App\Tests\EDOMPUnitTestCase;

/**
 * Class RepositoryUnitTestCase
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
abstract class RepositoryUnitTestCase extends EDOMPUnitTestCase
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
}
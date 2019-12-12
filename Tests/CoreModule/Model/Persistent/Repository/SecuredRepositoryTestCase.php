<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.11.19
 * Time: 17:20
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Repository\SecuredRepository;
use Nette\Security\User;

/**
 * Class SecuredRepositoryTestCase
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
abstract class SecuredRepositoryTestCase extends RepositoryUnitTestCase
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var SecuredRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->container->getByType(User::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->user = null;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.11.19
 * Time: 13:56
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\GeometricSequenceTemplate;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\GeometricSequenceTemplateRepository;

/**
 * Class GeometricSequenceTemplateRepositoryUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
final class GeometricSequenceTemplateRepositoryUnitTest extends SecuredRepositoryTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->getByType(GeometricSequenceTemplateRepository::class);
    }

    public function testFind(): void
    {
        $found = $this->repository->findAll();
        $this->assertCount(15, $found);
        foreach ($found as $item) {
            $this->assertInstanceOf(GeometricSequenceTemplate::class, $item);
        }
    }

    /**
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \Nette\Security\AuthenticationException
     */
    public function testFindAllowed(): void
    {
        $this->user->login('admin', '12345678');
        /** @var GeometricSequenceTemplate[] $found */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(15, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(GeometricSequenceTemplate::class, $item);
        }
        $this->user->logout(true);

        $this->user->login('jkohneke0@nba.com', '12345678');
        /** User[] $found */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(5, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(GeometricSequenceTemplate::class, $item);
        }
        $this->user->logout(true);

        $this->user->login('mhazzard1@wiley.com', '12345678');
        /** User[] $found */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(5, $found);
        $this->user->logout(true);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.11.19
 * Time: 13:55
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\QuadraticEquationTemplate;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\QuadraticEquationTemplateRepository;

/**
 * Class QuadraticEquationTemplateRepositoryUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
final class QuadraticEquationTemplateRepositoryUnitTest extends SecuredRepositoryTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->getByType(QuadraticEquationTemplateRepository::class);
    }

    public function testFind(): void
    {
        $found = $this->repository->findAll();
        $this->assertCount(10, $found);
        foreach ($found as $item) {
            $this->assertInstanceOf(QuadraticEquationTemplate::class, $item);
        }
    }

    /**
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \Nette\Security\AuthenticationException
     */
    public function testFindAllowed(): void
    {
        $this->user->login('admin', '12345678');
        /** @var QuadraticEquationTemplate[] $found */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(10, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(QuadraticEquationTemplate::class, $item);
        }
        $this->user->logout(true);

        $this->user->login('jkohneke0@nba.com', '12345678');
        /** User[] $found */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(10, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(QuadraticEquationTemplate::class, $item);
        }
        $this->user->logout(true);

        $this->user->login('mhazzard1@wiley.com', '12345678');
        /** User[] $found */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(0, $found);
        $this->user->logout(true);    }
}
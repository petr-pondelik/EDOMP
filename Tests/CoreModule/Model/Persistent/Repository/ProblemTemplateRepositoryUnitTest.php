<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.11.19
 * Time: 21:04
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;


use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\ProblemTemplateRepository;

/**
 * Class ProblemTemplateRepositoryUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
final class ProblemTemplateRepositoryUnitTest extends RepositoryUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->getByType(ProblemTemplateRepository::class);
    }

    public function testFind(): void
    {
        $this->assertEquals(42, $this->repository->getSequenceVal());
    }
}
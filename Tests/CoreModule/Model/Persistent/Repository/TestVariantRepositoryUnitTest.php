<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.11.19
 * Time: 18:41
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\TestVariant;
use App\CoreModule\Model\Persistent\Repository\TestVariantRepository;

/**
 * Class TestVariantRepositoryUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
class TestVariantRepositoryUnitTest extends RepositoryUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->getByType(TestVariantRepository::class);
    }

    public function testFind(): void
    {
        $found = $this->repository->find(1);
        $this->assertInstanceOf(TestVariant::class, $found);
        $this->assertEquals('A', (string) $found);
    }
}
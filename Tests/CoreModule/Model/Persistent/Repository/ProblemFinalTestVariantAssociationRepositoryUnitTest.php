<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.11.19
 * Time: 20:02
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemFinalTestVariantAssociation;
use App\CoreModule\Model\Persistent\Repository\ProblemFinalTestVariantAssociationRepository;

/**
 * Class ProblemFinalTestVariantAssociationRepositoryUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
final class ProblemFinalTestVariantAssociationRepositoryUnitTest extends RepositoryUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->getByType(ProblemFinalTestVariantAssociationRepository::class);
    }

    public function testFind(): void
    {
        $expected = [ '$$ 5 x = 15 - 4 + 2 $$' ];
        $filters = [ 'problemFinal.id' => 31 ];
        $found = $this->repository->findBy($filters);
        $this->assertCount(1, $found);
        foreach ($found as $key => $item) {
            /** @var ProblemFinalTestVariantAssociation $item */
            $this->assertInstanceOf(ProblemFinalTestVariantAssociation::class, $item);
            $this->assertEquals($expected[$key], (string) $item->getProblemFinal());
        }

        $expected = [
            '$$ 1 = \frac{x - 1 + 4}{x^2 + x} + \frac{ 3 }{x} $$',
            '$$ 1 = \frac{x - 1 + 4}{x^2 + x} + \frac{ 3 }{x} $$',
            '$$ 1 = \frac{x - 2 + 4}{x^2 + x} + \frac{ -1 }{x} $$'
        ];
        $filters = [ 'problemTemplate.id' => 15 ];
        $found = $this->repository->findBy($filters);
        $this->assertCount(3, $found);
        foreach ($found as $key => $item) {
            /** @var ProblemFinalTestVariantAssociation $item */
            $this->assertInstanceOf(ProblemFinalTestVariantAssociation::class, $item);
            $this->assertEquals($expected[$key], (string) $item->getProblemFinal());
        }
    }
}
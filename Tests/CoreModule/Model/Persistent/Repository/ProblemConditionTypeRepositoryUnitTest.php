<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.11.19
 * Time: 20:20
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemConditionType;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionTypeRepository;

/**
 * Class ProblemConditionTypeRepositoryUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
final class ProblemConditionTypeRepositoryUnitTest extends RepositoryUnitTestCase
{
    /**
     * @var ProblemConditionTypeRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->getByType(ProblemConditionTypeRepository::class);
    }

    public function testFind(): void
    {
        $expected = [ 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6 ];
        $found = $this->repository->findPairs([], 'id');
        $this->assertCount(6, $found);
        $this->assertIsArray($found);
        $this->assertEquals($expected, $found);

        $expected = [ 'Podmínka výsledku' ];
        $found = $this->repository->findNonValidation(1);
        $this->assertCount(1, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(ProblemConditionType::class, $item);
            $this->assertEquals($expected[$key], (string) $item);
        }
    }

    public function testFindAssocByProblemTypes(): void
    {
        $expected = [
            '1' => [ '1' ],
            '2' => [ '2' ]
        ];
        $found = $this->repository->findIdAssocByProblemTypes();
        $this->assertEquals($expected, $found);
    }
}
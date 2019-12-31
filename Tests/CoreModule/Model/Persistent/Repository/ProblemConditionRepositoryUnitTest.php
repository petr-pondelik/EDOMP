<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.11.19
 * Time: 20:39
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemCondition;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionRepository;

/**
 * Class ProblemConditionRepositoryUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
final class ProblemConditionRepositoryUnitTest extends RepositoryUnitTestCase
{
    /**
     * @var ProblemConditionRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->getByType(ProblemConditionRepository::class);
    }

    public function testFind(): void
    {
        $found = $this->repository->findOneBy([
            'problemConditionType.id' => 1,
            'accessor' => 1
        ]);
        $this->assertInstanceOf(ProblemCondition::class, $found);
        $this->assertEquals('Kladný', (string) $found);

        $expected = [
            1 => [
                0 => 'Bez podmínky',
                1 => 'Kladný',
                2 => 'Nulový',
                3 => 'Záporný'
            ],
            2 => [
                0 => 'Bez podmínky',
                1 => 'Kladný',
                2 => 'Nulový',
                3 => 'Záporný',
                4 => 'Celočíselný',
                5 => 'Kladný a odmocnitelný'
            ]
        ];

        $found = $this->repository->findAssocByTypeAndAccessor();
        $this->assertCount(2, $found);

        foreach ($found as $key => $item) {
            $this->assertCount(count($expected[$key]), $item);
            foreach ($item as $key2 => $item2) {
                $this->assertInstanceOf(ProblemCondition::class, $item2);
                $this->assertEquals($expected[$key][$key2], (string) $item2);
            }
        }
    }
}
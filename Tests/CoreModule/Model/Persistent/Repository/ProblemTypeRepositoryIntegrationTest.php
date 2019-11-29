<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.11.19
 * Time: 17:56
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemType;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use Nette\Utils\DateTime;

/**
 * Class ProblemTypeRepositoryIntegrationTest
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
final class ProblemTypeRepositoryIntegrationTest extends RepositoryIntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->getByType(ProblemTypeRepository::class);
    }

    /**
     * @throws \Exception
     */
    public function testFindAll(): void
    {
        $expected = [
            [
                'label' => 'Lineární rovnice',
                'keyLabel' => 'LinearEquation'
            ],
            [
                'label' => 'Kvadratická rovnice',
                'keyLabel' => 'QuadraticEquation'
            ],
            [
                'label' => 'Aritmetická posloupnost',
                'keyLabel' => 'ArithmeticSequence'
            ],
            [
                'label' => 'Geometrická posloupnost',
                'keyLabel' => 'GeometricSequence'
            ],
        ];

        $found = $this->repository->findAll();
        $this->assertCount(4, $found);
        foreach ($found as $key => $value) {
            $this->assertInstanceOf(ProblemType::class, $value);
            /** @var ProblemType $value */
            $this->assertEquals($expected[$key]['label'], (string) $value);
            $this->assertEquals($expected[$key]['keyLabel'], $value->getKeyLabel());
        }
    }
}
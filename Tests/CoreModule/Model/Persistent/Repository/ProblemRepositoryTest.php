<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.11.19
 * Time: 16:48
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemFinal;
use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use App\CoreModule\Model\Persistent\Repository\ProblemRepository;

/**
 * Class ProblemRepositoryTest
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
final class ProblemRepositoryTest extends SecuredRepositoryTestCase
{
    /**
     * @var ProblemRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->getByType(ProblemRepository::class);
    }

    public function testFindFiltered(): void
    {
        $found = $this->repository->findFiltered([
            'isTemplate' => '1',
            'isGenerated' => false,
            'createdBy' => '2'
        ]);
        $this->assertCount(30, $found);
        foreach ($found as $item) {
            $this->assertInstanceOf(ProblemTemplate::class, $item);
        }

        $found = $this->repository->findFiltered([
            'isTemplate' => '0',
            'isGenerated' => false,
            'createdBy' => '2'
        ]);
        $this->assertCount(1, $found);

        $found = $this->repository->findFiltered([
            'isTemplate' => '0',
            'createdBy' => '2'
        ]);
        $this->assertCount(11, $found);
        foreach ($found as $item) {
            $this->assertInstanceOf(ProblemFinal::class, $item);
        }

        $found = $this->repository->findFiltered([
            'isTemplate' => '0'
        ]);
        $this->assertCount(33, $found);
        foreach ($found as $item) {
            $this->assertInstanceOf(ProblemFinal::class, $item);
        }

        $found = $this->repository->findFiltered([
            'isTemplate' => '1'
        ]);
        $this->assertCount(90, $found);
        foreach ($found as $item) {
            $this->assertInstanceOf(ProblemTemplate::class, $item);
        }

        $found = $this->repository->findFiltered([
            'isTemplate' => '0',
            'isGenerated' => false
        ]);
        $this->assertCount(3, $found);
        foreach ($found as $item) {
            $this->assertInstanceOf(ProblemFinal::class, $item);
        }

        $found = $this->repository->findFiltered([
            'isTemplate' => '1',
            'problemType' => [ '1', '2' ],
            'difficulty' => [ '1' ],
            'isGenerated' => false,
            'createdBy' => 2
        ]);
        $this->assertCount(13, $found);
        foreach ($found as $item) {
            $this->assertInstanceOf(ProblemTemplate::class, $item);
        }

        $found = $this->repository->findFiltered([
            'isTemplate' => '1',
            'problemType' => [ '1', '2' ],
            'difficulty' => [ '1' ],
            'isGenerated' => false,
            'createdBy' => 2,
            'conditionType' => [ ['1'], ['5'] ]
        ]);
        $this->assertCount(5, $found);
        foreach ($found as $item) {
            $this->assertInstanceOf(ProblemTemplate::class, $item);
        }
    }
}
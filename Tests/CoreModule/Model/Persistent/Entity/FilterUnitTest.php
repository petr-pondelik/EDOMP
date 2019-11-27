<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.11.19
 * Time: 17:37
 */

declare(strict_types = 1);

namespace App\Tests\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Entity\Filter;
use App\Tests\Traits\ProblemTypeSetUpMockTrait;
use App\Tests\Traits\TestMockSetUpTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class FilterUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
class FilterUnitTest extends PersistentEntityTestCase
{
    use TestMockSetUpTrait;
    use ProblemTypeSetUpMockTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTestMock();
        $this->setUpProblemTypeMock();
    }

    public function testValidState(): void
    {
        $entity = new Filter();
        $selectedFilters = [ 'isGenerated' => false ];
        $selectedProblems = [1, 2];
        $seq = 0;

        $this->assertInstanceOf(Filter::class, $entity);

        $entity->setTest($this->testMock);
        $entity->setSelectedFilters($selectedFilters);
        $entity->setSelectedProblems($selectedProblems);
        $entity->setSeq($seq);



        $entity->setProblemTypes(new ArrayCollection([$this->problemTypeMock]));

    }

    public function testInvalidState(): void
    {
        $entity = new Filter();
    }
}
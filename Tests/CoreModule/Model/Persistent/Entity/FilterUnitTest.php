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
use App\Tests\MockTraits\Entity\DifficultySetUpTrait;
use App\Tests\MockTraits\Entity\ProblemConditionSetUpTrait;
use App\Tests\MockTraits\Entity\ProblemTypeSetUpMockTrait;
use App\Tests\MockTraits\Entity\SubThemeMockSetUpTrait;
use App\Tests\MockTraits\Entity\TestMockSetUpTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class FilterUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
final class FilterUnitTest extends PersistentEntityTestCase
{
    use TestMockSetUpTrait;
    use ProblemTypeSetUpMockTrait;
    use SubThemeMockSetUpTrait;
    use ProblemConditionSetUpTrait;
    use DifficultySetUpTrait;

    /**
     * @var array
     */
    protected $errorMessages = [
        'SelectedFilters can\'t be blank.',
        'Test can\'t be blank.',
        'Seq can\'t be blank.',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTestMock();
        $this->setUpProblemTypeMock();
        $this->setUpSubThemeMock();
        $this->setUpProblemConditionMock();
        $this->setUpDifficultyMock();
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

        $this->assertFalse($entity->isTeacherLevelSecured());
        $this->assertEquals($selectedFilters, $entity->getSelectedFilters());
        $this->assertEquals($selectedProblems, $entity->getSelectedProblems());
        $this->assertEquals($seq, $entity->getSeq());
        $this->assertEquals($entity->getProblemTypes(), new ArrayCollection([]));
        $this->assertEquals($entity->getSubThemes(), new ArrayCollection([]));
        $this->assertEquals($entity->getProblemConditions(), new ArrayCollection([]));
        $this->assertEquals($entity->getDifficulties(), new ArrayCollection([]));

        $entity->setProblemTypes(new ArrayCollection([$this->problemTypeMock]));
        $entity->setSubThemes(new ArrayCollection([$this->subThemeMock]));
        $entity->setProblemConditions(new ArrayCollection([$this->problemConditionMock]));
        $entity->setDifficulties(new ArrayCollection([$this->difficultyMock]));

        $this->assertEquals(new ArrayCollection([$this->problemTypeMock]), $entity->getProblemTypes());
        $this->assertEquals(new ArrayCollection([$this->subThemeMock]), $entity->getSubThemes());
        $this->assertEquals(new ArrayCollection([$this->problemConditionMock]), $entity->getProblemConditions());
        $this->assertEquals(new ArrayCollection([$this->difficultyMock]), $entity->getDifficulties());

        $this->assertValidByValidator($entity);
    }

    public function testInvalidState(): void
    {
        $entity = new Filter();
        $this->assertValidatorViolations($entity);
    }
}
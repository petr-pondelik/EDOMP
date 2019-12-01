<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.12.19
 * Time: 12:12
 */

namespace App\Tests\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\Filter;
use App\CoreModule\Model\Persistent\Entity\Test;
use App\CoreModule\Model\Persistent\Functionality\FilterFunctionality;
use App\Tests\MockTraits\Repository\DifficultyRepositoryMockTrait;
use App\Tests\MockTraits\Repository\ProblemConditionRepositoryMockTrait;
use App\Tests\MockTraits\Repository\ProblemTypeRepositoryMockTrait;
use App\Tests\MockTraits\Repository\SubThemeRepositoryMockTrait;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;

/**
 * Class FilterFunctionalityUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Functionality
 */
final class FilterFunctionalityUnitTest extends FunctionalityUnitTestCase
{
    use ProblemTypeRepositoryMockTrait;
    use DifficultyRepositoryMockTrait;
    use SubThemeRepositoryMockTrait;
    use ProblemConditionRepositoryMockTrait;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpProblemTypeRepositoryMock();
        $this->setUpDifficultyRepositoryMock();
        $this->setUpSubThemeRepositoryMock();
        $this->setUpProblemConditionRepositoryMock();
        $this->functionality = new FilterFunctionality(
            $this->em, $this->problemTypeRepositoryMock, $this->difficultyRepositoryMock,
            $this->subThemeRepositoryMock, $this->problemConditionRepositoryMock
        );
    }

    /**
     * @throws \Exception
     */
    public function testCreate(): void
    {
        // Prepare Test entity
        $test = new Test();
        $test->setCreated(DateTime::from($this->dateTimeStr));

        // Data for Filter create
        $data = ArrayHash::from([
            'selectedFilters' => [
                'isGenerated' => false,
                'createdBy'=> 2,
                'isTemplate' => 1,
                'problemType' => [1],
                'difficulty' => [],
                'subTheme' => [],
                'conditionType' => [
                    1 => [], 2 => [], 3 => [], 4 => [], 5 => [], 6 => []
                ]
            ],
            'selectedProblems' => [],
            'test' => $test,
            'seq' => 0,
            'created' => DateTime::from($this->dateTimeStr)
        ]);

        // Prepare expected Filter
        $expected = new Filter();
        $expected->setSelectedFilters($data['selectedFilters']);
        $expected->setSelectedProblems($data['selectedProblems']);
        $expected->setTest($data['test']);
        $expected->setSeq($data['seq']);
        foreach ($data['selectedFilters']['problemType'] as $problemTypeId) {
            $expected->addProblemType($this->problemTypeRepositoryMock->find($problemTypeId));
        }
        $expected->setCreated(DateTime::from($this->dateTimeStr));

        // Create Filter and test it against expected Filter
        $created = $this->functionality->create($data);
        $this->assertEquals($expected, $created);
    }

    public function testUpdate(): void
    {
        $this->assertNull($this->functionality->update(1, []));
    }
}
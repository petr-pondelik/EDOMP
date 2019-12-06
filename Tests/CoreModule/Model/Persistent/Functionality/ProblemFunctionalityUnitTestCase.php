<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.12.19
 * Time: 12:42
 */

namespace App\Tests\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\ProblemCondition;
use App\Tests\MockTraits\Repository\DifficultyRepositoryMockTrait;
use App\Tests\MockTraits\Repository\ProblemConditionRepositoryMockTrait;
use App\Tests\MockTraits\Repository\ProblemConditionTypeRepositoryMockTrait;
use App\Tests\MockTraits\Repository\ProblemTemplateRepositoryMockTrait;
use App\Tests\MockTraits\Repository\ProblemTypeRepositoryMockTrait;
use App\Tests\MockTraits\Repository\SubThemeRepositoryMockTrait;
use App\Tests\MockTraits\Repository\TemplateJsonDataRepositoryMockTrait;
use App\Tests\MockTraits\Repository\UserRepositoryMockTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Utils\DateTime;

/**
 * Class ProblemFunctionalityUnitTestCase
 * @package App\Tests\CoreModule\Model\Persistent\Functionality
 */
abstract class ProblemFunctionalityUnitTestCase extends FunctionalityUnitTestCase
{
    use ProblemTypeRepositoryMockTrait;
    use DifficultyRepositoryMockTrait;
    use SubThemeRepositoryMockTrait;
    use TemplateJsonDataRepositoryMockTrait;
    use ProblemConditionTypeRepositoryMockTrait;
    use ProblemConditionRepositoryMockTrait;
    use ProblemTemplateRepositoryMockTrait;
    use UserRepositoryMockTrait;

    /**
     * @var ProblemCondition
     */
    protected $thirdProblemCondition;

    /**
     * @var ProblemCondition
     */
    protected $fourthProblemCondition;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        // SetUp default repositories states
        $this->setUpProblemTypeRepositoryMock();
        $this->setUpDifficultyRepositoryMock();
        $this->setUpSubThemeRepositoryMock();
        $this->setUpTemplateJsonDataRepository();
        $this->setUpProblemConditionTypeRepositoryMock();
        $this->setUpProblemConditionRepositoryMock();
        $this->setUpProblemTemplateRepositoryMock();
        $this->setUpUserRepositoryMock();

        // Set ProblemCondition type to existing problemConditions
        $this->firstProblemCondition->setProblemConditionType($this->firstProblemConditionType);
        $this->secondProblemCondition->setProblemConditionType($this->secondProblemConditionType);

        // Create third ProblemCondition
        $thirdProblemCondition = new ProblemCondition();
        $thirdProblemCondition->setId(3);
        $thirdProblemCondition->setCreated(DateTime::from($this->dateTimeStr));
        $thirdProblemCondition->setAccessor(0);
        $thirdProblemCondition->setLabel('THIRD_PROBLEM_CONDITION');
        $thirdProblemCondition->setProblemConditionType($this->secondProblemConditionType);
        $this->thirdProblemCondition = $thirdProblemCondition;

        // Create fourth ProblemCondition
        $fourthProblemCondition = new ProblemCondition();
        $fourthProblemCondition->setId(4);
        $fourthProblemCondition->setCreated(DateTime::from($this->dateTimeStr));
        $fourthProblemCondition->setAccessor(1);
        $fourthProblemCondition->setLabel('FOURTH_PROBLEM_CONDITION');
        $fourthProblemCondition->setProblemConditionType($this->secondProblemConditionType);
        $this->fourthProblemCondition = $fourthProblemCondition;

        // Set problemConditionType to firstProblemType
        $this->firstProblemType->setConditionTypes(new ArrayCollection([
            $this->firstProblemConditionType, $this->secondProblemConditionType
        ]));

        // Set problemConditionType to secondProblemType
        $this->secondProblemType->setConditionTypes(new ArrayCollection([
            $this->firstProblemConditionType, $this->secondProblemConditionType
        ]));

        // Set problemConditionRepositoryMock expected return values on findOneBy method
        $this->problemConditionRepositoryMock->method('findOneBy')
            ->willReturnCallback(function ($arg) {
                switch ($arg){
                    case [
                        'problemConditionType.id' => 1,
                        'accessor' => 0
                    ]:
                        return $this->firstProblemCondition;
                    case [
                        'problemConditionType.id' => 1,
                        'accessor' => 1
                    ]:
                        return $this->secondProblemCondition;
                    case [
                        'problemConditionType.id' => 2,
                        'accessor' => 0
                    ]:
                        return $this->thirdProblemCondition;
                    case [
                        'problemConditionType.id' => 2,
                        'accessor' => 1
                    ]:
                        return $this->fourthProblemCondition;
                }
                return null;
            });

        // Set expected return values for TemplateJsonDataRepository findOneBy method
        $this->templateJsonDataRepositoryMock->method('findOneBy')
            ->willReturnCallback(function ($arg) {
                switch ($arg){
                    case [ 'templateId' => 1 ]:
                        return $this->firstTemplateJsonData;
                }
                return false;
            });
    }
}
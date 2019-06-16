<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.6.19
 * Time: 17:09
 */

namespace Tests\Model\Functionality;

use App\Model\Entity\Category;
use App\Model\Entity\Difficulty;
use App\Model\Entity\ProblemCondition;
use App\Model\Entity\ProblemConditionType;
use App\Model\Entity\ProblemType;
use App\Model\Entity\SubCategory;
use App\Model\Entity\TemplateJsonData;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TemplateJsonDataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ProblemTemplateFunctionalityTest
 * @package App\AppTests\Model\Functionality
 */
abstract class ProblemFunctionalityTestCase extends FunctionalityTestCase
{
    /**
     * @var MockObject
     */
    protected $problemTypeRepositoryMock;

    /**
     * @var MockObject
     */
    protected $problemConditionRepositoryMock;

    /**
     * @var MockObject
     */
    protected $difficultyRepositoryMock;

    /**
     * @var MockObject
     */
    protected $subCategoryRepositoryMock;

    /**
     * @var MockObject
     */
    protected $templateJsonDataRepositoryMock;

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        // Create ProblemType
        $problemType = new ProblemType();
        $problemType->setLabel('TEST_PROBLEM_TYPE');
        $problemType->setId(1);

        // Create new ProblemType
        $newProblemType = new ProblemType();
        $newProblemType->setLabel('TEST_PROBLEM_TYPE_NEW');
        $newProblemType->setId(2);

        // Create Difficulty
        $difficulty = new Difficulty();
        $difficulty->setLabel('TEST_DIFFICULTY');
        $difficulty->setId(1);

        // Create new Difficulty
        $newDifficulty = new Difficulty();
        $newDifficulty->setLabel('TEST_DIFFICULTY_NEW');
        $newDifficulty->setId(2);

        // Create Category
        $category = new Category();
        $category->setLabel('TEST_CATEGORY');
        $category->setId(1);

        // Create SubCategory
        $subCategory = new SubCategory();
        $subCategory->setLabel('TEST_SUB_CATEGORY');
        $subCategory->setCategory($category);

        // Create new SubCategory
        $newSubCategory = new SubCategory();
        $newSubCategory->setLabel('TEST_SUB_CATEGORY_NEW');
        $newSubCategory->setCategory($category);

        // Create first ProblemConditionType
        $firstProblemConditionType = new ProblemConditionType();
        $firstProblemConditionType->setLabel('TEST_PROBLEM_CONDITION_TYPE_FIRST');
        $firstProblemConditionType->setId(1);

        // Create second ProblemConditionType
        $secondProblemConditionType = new ProblemConditionType();
        $secondProblemConditionType->setLabel('TEST_PROBLEM_CONDITION_TYPE_SECOND');
        $secondProblemConditionType->setId(2);

        // Create first ProblemCondition
        $firstProblemCondition = new ProblemCondition();
        $firstProblemCondition->setProblemConditionType($firstProblemConditionType);
        $firstProblemCondition->setAccessor(0);
        $firstProblemCondition->setLabel('TEST_PROBLEM_CONDITION_FIRST');
        $firstProblemCondition->setId(1);

        // Create second ProblemCondition
        $secondProblemCondition = new ProblemCondition();
        $secondProblemCondition->setProblemConditionType($firstProblemConditionType);
        $secondProblemCondition->setAccessor(0);
        $secondProblemCondition->setLabel('TEST_PROBLEM_CONDITION');
        $secondProblemCondition->setId(1);

        // Create third ProblemCondition
        $thirdProblemCondition = new ProblemCondition();
        $thirdProblemCondition->setProblemConditionType($secondProblemConditionType);
        $thirdProblemCondition->setAccessor(1);
        $thirdProblemCondition->setLabel('TEST_PROBLEM_CONDITION_NEW');
        $thirdProblemCondition->setId(2);

        // Create fourth ProblemCondition
        $fourthProblemCondition = new ProblemCondition();
        $fourthProblemCondition->setProblemConditionType($secondProblemConditionType);
        $fourthProblemCondition->setAccessor(0);
        $fourthProblemCondition->setLabel('TEST_PROBLEM_CONDITION');
        $fourthProblemCondition->setId(1);

        // Finalize ProblemTypes
        $problemType->setConditionTypes(new ArrayCollection( [$firstProblemConditionType, $secondProblemConditionType] ));
        $newProblemType->setConditionTypes(new ArrayCollection( [$firstProblemConditionType, $secondProblemConditionType] ));

        // Create TemplateJsonData
        $templateJsonData = new TemplateJsonData();
        $templateJsonData->setJsonData('[]');
        $templateJsonData->setTemplateId(1);
        $templateJsonData->setId(1);

        // Mock the ProblemTypeRepository
        $this->problemTypeRepositoryMock = $this->getMockBuilder(ProblemTypeRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for ProblemTypeRepository
        $this->problemTypeRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use (
                $problemType, $newProblemType
            ) {
                $map = [
                    1 => $problemType,
                    2 => $newProblemType,
                    50 => null
                ];
                return $map[$arg];
            });

        // Mock the ProblemConditionRepository
        $this->problemConditionRepositoryMock = $this->getMockBuilder(ProblemConditionRepository::class)
            ->setMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for ProblemConditionRepository findOneBy method
        $this->problemConditionRepositoryMock->expects($this->any())
            ->method('findOneBy')
            ->willReturnCallback(static function ($arg) use (
                $firstProblemCondition, $secondProblemCondition, $thirdProblemCondition, $fourthProblemCondition
            ) {
                switch ($arg){
                    case [
                        'problemConditionType.id' => 1,
                        'accessor' => 0
                    ]:
                        return $firstProblemCondition;
                    case [
                        'problemConditionType.id' => 1,
                        'accessor' => 1
                    ]:
                        return $secondProblemCondition;
                    case [
                        'problemConditionType.id' => 2,
                        'accessor' => 0
                    ]:
                        return $thirdProblemCondition;
                    case [
                        'problemConditionType.id' => 2,
                        'accessor' => 1
                    ]:
                        return $fourthProblemCondition;
                }
                return null;
            });

        // Mock the DifficultyRepository
        $this->difficultyRepositoryMock = $this->getMockBuilder(DifficultyRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for DifficultyRepository
        $this->difficultyRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use (
                $difficulty, $newDifficulty
            ) {
                $map = [
                    1 => $difficulty,
                    2 => $newDifficulty,
                    50 => null
                ];
                return $map[$arg];
            });

        // Mock the SubCategoryRepository
        $this->subCategoryRepositoryMock = $this->getMockBuilder(SubCategoryRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for SubCategoryRepository
        $this->subCategoryRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use (
                $subCategory, $newSubCategory
            ) {
                $map = [
                    1 => $subCategory,
                    2 => $newSubCategory,
                    50 => null
                ];
                return $map[$arg];
            });

        // Mock the TemplateJsonDataRepository
        $this->templateJsonDataRepositoryMock = $this->getMockBuilder(TemplateJsonDataRepository::class)
            ->setMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for TemplateJsonDataRepository findOneBy method
        $this->templateJsonDataRepositoryMock->expects($this->any())
            ->method('findOneBy')
            ->willReturnCallback(static function ($arg) use (
                $templateJsonData
            ) {
                switch ($arg){
                    case [ 'templateId' => 1 ]:
                        return $templateJsonData;
                }
                return false;
            });
    }
}
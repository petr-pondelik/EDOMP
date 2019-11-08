<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 12.6.19
 * Time: 12:44
 */

namespace Tests\Model\Services;


use App\Helpers\ConstHelper;
use App\Helpers\StringsHelper;
use App\Model\Entity\Category;
use App\Model\Entity\Difficulty;
use App\Model\Entity\LinearEqTempl;
use App\Model\Entity\ProblemType;
use App\Model\Entity\SubCategory;
use App\Model\Repository\ProblemTemplateRepository;
use App\Services\ProblemGenerator;
use Nette\Utils\Strings;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class GeneratorServiceTest
 * @package App\AppTests\Services
 */
class GeneratorServiceTest extends TestCase
{
    /**
     * @var ProblemGenerator
     */
    protected $generatorService;

    /**
     * @var MockObject
     */
    protected $problemTemplateRepositoryMock;

    public function setUp(): void
    {
        parent::setUp();

        // Instantiate StringsHelper
        $stringsHelper = new StringsHelper();

        // Instantiate ConstHelper
        $constHelper = new ConstHelper();

        // Mock the ProblemTemplateRepository
        $this->problemTemplateRepositoryMock = $this->getMockBuilder(ProblemTemplateRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Instantiate tested class
        $this->generatorService = new ProblemGenerator($this->problemTemplateRepositoryMock, $stringsHelper, $constHelper);
    }

    /**
     * @throws \Exception
     */
    public function testGenerateProblemFinal(): void
    {
        // Create ProblemType
        $problemType = new ProblemType();
        $problemType->setLabel('TEST_PROBLEM_TYPE');
        $problemType->setId(1);

        // Create Difficulty
        $difficulty = new Difficulty();
        $difficulty->setLabel('TEST_DIFFICULTY');
        $difficulty->setId(1);

        // Create Category
        $category = new Category();
        $category->setLabel('TEST_CATEGORY');
        $category->setId(1);

        // Create SubCategory
        $subCategory = new SubCategory();
        $subCategory->setLabel('TEST_SUB_CATEGORY');
        $subCategory->setCategory($category);

        // Create ProblemTemplate
        $problemTemplate = new LinearEqTempl();
        $problemTemplate->setBody('$$ \big(\frac{15 x + 5 x}{2}\big) + <par min="0" max="15"/> = <par min="0" max="10"/> $$');
        $problemTemplate->setProblemType($problemType);
        $problemTemplate->setDifficulty($difficulty);
        $problemTemplate->setSubCategory($subCategory);
        $problemTemplate->setId(1);

        // Set expected return values for ProblemTemplateRepository's find method
        $this->problemTemplateRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($problemTemplate) {
                switch ($arg){
                    case 1: return $problemTemplate;
                    default: return null;
                }
            });

        // Generate ProblemFinal from entered ProblemTemplate (without condition matches)
        $problemFinal = $this->generatorService->generateProblemFinal($problemTemplate);

        // Get values of generated parameters
        $generatedPart = Strings::before( Strings::after($problemFinal, '$$ \big(\frac{15 x + 5 x}{2}\big) + '), ' $$' );
        $generatedParameters = Strings::match($generatedPart, '~(\d)\s=\s(\d)~');

        // Test if the parameter's values are valid
        $this->assertGreaterThanOrEqual(0, $generatedParameters[1]);
        $this->assertLessThanOrEqual(15, $generatedParameters[1]);
        $this->assertGreaterThanOrEqual(0, $generatedParameters[2]);
        $this->assertLessThanOrEqual(10, $generatedParameters[2]);

        // Set condition Matches to the ProblemTemplate
        $problemTemplate->setMatches('[{"p0":0,"p1":0},{"p0":1,"p1":1},{"p0":2,"p1":2},{"p0":3,"p1":3},{"p0":4,"p1":4},{"p0":5,"p1":5},{"p0":6,"p1":6},{"p0":7,"p1":7},{"p0":8,"p1":8},{"p0":9,"p1":9},{"p0":10,"p1":10}]');

        // Prepare expected data
        $expectedParameters = [
            [ 'p0' => 0, 'p1' => 0 ], [ 'p0' => 1, 'p1' => 1 ], [ 'p0' => 2, 'p1' => 2 ], [ 'p0' => 3, 'p1' => 3 ],
            [ 'p0' => 4, 'p1' => 4 ], [ 'p0' => 5, 'p1' => 5 ], [ 'p0' => 6, 'p1' => 6 ], [ 'p0' => 7, 'p1' => 7 ],
            [ 'p0' => 8, 'p1' => 8 ], [ 'p0' => 9, 'p1' => 9 ], [ 'p0' => 10, 'p1' => 10 ]
        ];

        // Generate ProblemFinal from entered ProblemTemplate (with condition matches)
        $problemFinal = $this->generatorService->generateProblemFinal($problemTemplate);

        // Get values of generated parameters
        $generatedPart = Strings::before( Strings::after($problemFinal, '$$ \big(\frac{15 x + 5 x}{2}\big) + '), ' $$' );
        $generatedParameters = Strings::match($generatedPart, '~(\d)\s=\s(\d)~');

        // Test if the parameter's values are valid
        $this->assertContains( [ 'p0' => $generatedParameters[1], 'p1' => $generatedParameters[2] ], $expectedParameters );
    }
}
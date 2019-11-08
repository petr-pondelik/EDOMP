<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 13.6.19
 * Time: 22:11
 */

namespace Tests\Model\Services;


use App\Model\Entity\Category;
use App\Model\Entity\Difficulty;
use App\Model\Entity\Group;
use App\Model\Entity\LinearEqTempl;
use App\Model\Entity\Logo;
use App\Model\Entity\ProblemFinal;
use App\Model\Entity\ProblemTemplate;
use App\Model\Entity\ProblemFinalTestVariantAssociation;
use App\Model\Entity\ProblemType;
use App\Model\Entity\SubCategory;
use App\Model\Entity\SuperGroup;
use App\Model\Entity\Test;
use App\Model\Functionality\ProblemFinalFunctionality;
use App\Model\Functionality\TestFunctionality;
use App\Model\Repository\ProblemRepository;
use App\Model\Repository\TestRepository;
use App\Services\ProblemGenerator;
use App\Services\TestGeneratorService;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class TestBuilderServiceTest
 * @package App\AppTests\Services
 */
class TestBuilderServiceTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $problemRepositoryMock;

    /**
     * @var MockObject
     */
    protected $testRepositoryMock;

    /**
     * @var MockObject
     */
    protected $problemFinalFunctionalityMock;

    /**
     * @var MockObject
     */
    protected $testFunctionalityMock;

    /**
     * @var MockObject
     */
    protected $generatorServiceMock;

    /**
     * @var TestGeneratorService
     */
    protected $testBuilderService;

    /**
     * @var Group
     */
    protected $group;

    /**
     * @var Logo
     */
    protected $logo;

    /**
     * @var Category
     */
    protected $category;

    /**
     * @var Difficulty
     */
    protected $difficulty;

    /**
     * @var SubCategory
     */
    protected $subCategory;

    /**
     * @var ProblemType
     */
    protected $problemType;

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        // Create SuperGroup
        $superGroup = new SuperGroup();
        $superGroup->setLabel('TEST_SUPER_GROUP');
        $superGroup->setId(1);

        // Create Group
        $group = new Group();
        $group->setLabel('TEST_GROUP');
        $group->setSuperGroup($superGroup);
        $group->setId(1);
        $this->group = $group;

        // Create Logo
        $logo = new Logo();
        $logo->setLabel('TEST_LOGO');
        $logo->setExtensionTmp('TEST_EXTENSION_TMP');
        $logo->setId(1);
        $this->logo = $logo;

        // Create Category
        $category = new Category();
        $category->setLabel('TEST_CATEGORY');
        $category->setId(1);
        $this->category = $category;

        // Create Difficulty
        $difficulty = new Difficulty();
        $difficulty->setLabel('TEST_DIFFICULTY');
        $difficulty->setId(1);
        $this->difficulty = $difficulty;

        // Create SubCategory
        $subCategory = new SubCategory();
        $subCategory->setLabel('TEST_SUBCATEGORY');
        $subCategory->setCategory($category);
        $subCategory->setId(1);
        $this->subCategory = $subCategory;

        // Create ProblemType
        $problemType = new ProblemType();
        $problemType->setLabel('TEST_PROBLEM_TYPE');
        $problemType->setId(1);
        $this->problemType = $problemType;

        // Create ProblemFinal1
        $problemFinal1 = new ProblemFinal();
        $problemFinal1->setBody('TEST_BODY_1');
        $problemFinal1->setSubCategory($subCategory);
        $problemFinal1->setDifficulty($difficulty);
        $problemFinal1->setProblemType($problemType);
        $problemFinal1->setCreated(new DateTime('2000-01-01'));
        $problemFinal1->setId(1);

        // Create ProblemFinal2
        $problemFinal2 = new ProblemFinal();
        $problemFinal2->setBody('TEST_BODY_2');
        $problemFinal2->setSubCategory($subCategory);
        $problemFinal2->setDifficulty($difficulty);
        $problemFinal2->setProblemType($problemType);
        $problemFinal2->setCreated(new DateTime('2000-01-01'));
        $problemFinal2->setId(2);

        // Create ProblemFinal3
        $problemFinal3 = new ProblemFinal();
        $problemFinal3->setBody('TEST_BODY_3');
        $problemFinal3->setSubCategory($subCategory);
        $problemFinal3->setDifficulty($difficulty);
        $problemFinal3->setProblemType($problemType);
        $problemFinal3->setCreated(new DateTime('2000-01-01'));
        $problemFinal3->setId(3);

        // Create ProblemFinal4
        $problemFinal4 = new ProblemFinal();
        $problemFinal4->setBody('TEST_BODY_4');
        $problemFinal4->setSubCategory($subCategory);
        $problemFinal4->setDifficulty($difficulty);
        $problemFinal4->setProblemType($problemType);
        $problemFinal4->setCreated(new DateTime('2000-01-01'));
        $problemFinal4->setId(4);

        // Create ProblemFinal5
        $problemFinal5 = new ProblemFinal();
        $problemFinal5->setBody('TEST_BODY_5');
        $problemFinal5->setSubCategory($subCategory);
        $problemFinal5->setDifficulty($difficulty);
        $problemFinal5->setProblemType($problemType);
        $problemFinal5->setCreated(new DateTime('2000-01-01'));
        $problemFinal5->setId(5);

        // Create ProblemTemplate
        $problemTemplate = new LinearEqTempl();
        $problemTemplate->setBody('TEST_BODY_6');
        $problemTemplate->setSubCategory($subCategory);
        $problemTemplate->setDifficulty($difficulty);
        $problemTemplate->setProblemType($problemType);
        $problemTemplate->setCreated(new DateTime('2000-01-01'));
        $problemTemplate->setVariable('T');
        $problemTemplate->setId(6);

        // Mock the ProblemRepository
        $this->problemRepositoryMock = $this->getMockBuilder(ProblemRepository::class)
            ->setMethods(['find', 'findFiltered'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for ProblemRepository's find method
        $this->problemRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use (
                $problemFinal1, $problemFinal2, $problemFinal3, $problemFinal4, $problemFinal5, $problemTemplate
            ) {
                switch ($arg){
                    case 1: return $problemFinal1;
                    case 2: return $problemFinal2;
                    case 3: return $problemFinal3;
                    case 4: return $problemFinal4;
                    case 5: return $problemFinal5;
                    case 6: return $problemTemplate;
                    default: return null;
                }
            });

        // Set expected return values for ProblemRepository's findFiltered method
        $this->problemRepositoryMock->expects($this->any())
            ->method('findFiltered')
            ->willReturnCallback(
                static function ($filters) use (
                    $problemFinal1, $problemFinal2, $problemFinal3, $problemFinal4, $problemFinal5
                ) {
                    switch ($filters){
                        case [
                            'is_template' => 0,
                            'problem_type_id' => 2,
                            'difficulty_id' => 0,
                            'sub_category_id' => 0
                        ]:
                            return [
                                $problemFinal1, $problemFinal2, $problemFinal3, $problemFinal4, $problemFinal5
                            ];
                        default: return null;
                    }
            });

        // Mock the TestRepository
        $this->testRepositoryMock = $this->getMockBuilder(TestRepository::class)
            ->setMethods(['getSequenceVal'])
            ->disableOriginalConstructor()
            ->getMock();

        // Mock the ProblemFinalFunctionality
        $this->problemFinalFunctionalityMock = $this->getMockBuilder(ProblemFinalFunctionality::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for ProblemFinalFunctionality's create method
        $this->problemFinalFunctionalityMock->expects($this->any())
            ->method('create')
            ->willReturnCallback(static function (ArrayHash $data, array $conditions = null, bool $flush = true) use ($problemType, $difficulty, $subCategory, $problemTemplate) {
                $problem = new ProblemFinal();
                $problem->setTextBefore($data->textBefore);
                $problem->setBody($data->body);
                $problem->setTextAfter($data->textAfter);

                if(isset($data->result)){
                    $problem->setResult($data->result);
                }
                if(isset($data->is_generated)){
                    $problem->setIsGenerated($data->is_generated);
                }
                if(isset($data->variable)){
                    $problem->setVariable($data->variable);
                }
                if(isset($data->first_n)){
                    $problem->setFirstN($data->first_n);
                }
                if(isset($data->created)){
                    $problem->setCreated($data->created);
                }

                $problem->setProblemType($problemType);
                $problem->setDifficulty($difficulty);
                $problem->setSubCategory($subCategory);

                if(isset($data->problem_template_id)){
                    $problem->setProblemTemplate($problemTemplate);
                }

                return $problem;
            });

        // Mock the TestFunctionality
        $this->testFunctionalityMock = $this->getMockBuilder(TestFunctionality::class)
            ->setMethods(['create', 'attachProblem'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for TestFunctionality's create method
        $this->testFunctionalityMock->expects($this->any())
            ->method('create')
            ->willReturnCallback(static function ($arg) use ($group, $logo) {
                $test = new Test();
                $test->setLogo($logo);
                $test->setGroups(new ArrayCollection([$group]));
                $test->setSchoolYear($arg->school_year);
                $test->setTerm($arg->term);
                $test->setCreated(new DateTime('2000-01-01'));
                $test->setTestNumber($arg->test_number);
                $test->setIntroductionText($arg->introduction_text);
                $test->setId(1);
                return $test;
            });

        // Set expected return values for TestFunctionality's attachProblem method
        $this->testFunctionalityMock->expects($this->any())
            ->method('attachProblem')
            ->willReturnCallback(static function (
                Test $test, ProblemFinal $problem, string $variant, ProblemTemplate $template = null, bool $newPage = false
            ) {
                $association = new ProblemFinalTestVariantAssociation();
                $association->setTest($test);
                $association->setProblem($problem);
                $association->setVariant($variant);
                if($template !== null){
                    $association->setProblemTemplate($template);
                }
                $association->setNextPage($newPage);
                $test->addProblemAssociation($association);
                $association->setCreated(new DateTime('2000-01-01'));
                return $test;
            });

        // Mock the GeneratorService
        $this->generatorServiceMock = $this->getMockBuilder(ProblemGenerator::class)
            ->setMethods(['generateInteger', 'generateProblemFinal'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for GeneratorService's generateInteger method
        $this->generatorServiceMock->expects($this->any())
            ->method('generateInteger')
            ->willReturnCallback(static function ($min, $max) {
                if(isset($min, $max)) { return mt_rand($min, $max); }
                if($min !== null) { return mt_rand($min, PHP_INT_MAX); }
                if($max !== null) { return mt_rand(0, $max); }
                return mt_rand();
            });

        // Set expected return values for GeneratorService's generateInteger method
        $this->generatorServiceMock->expects($this->any())
            ->method('generateProblemFinal')
            ->willReturnCallback(static function (ProblemTemplate $problemTemplate) {
                return 'TEST_BODY_6_FINAL';
            });

        // Instantiate tested class
        $this->testBuilderService = new TestGeneratorService
        (
            $this->problemRepositoryMock, $this->testRepositoryMock, $this->problemFinalFunctionalityMock,
            $this->testFunctionalityMock, $this->generatorServiceMock
        );
    }

    /**
     * @throws \App\Exceptions\ProblemDuplicityException
     * @throws \Nette\Utils\JsonException
     * @throws \Exception
     */
    public function testBuildTest(): void
    {
        // Data for creation of Test entity
        $data = ArrayHash::from([
            'variants' => 2,
            'problems_cnt' => 1,
            'logo_file_hidden' => 1,
            'groups' => [1],
            'test_term' => '1. pololetí',
            'school_year' => '2018/2019',
            'test_number' => 1,
            'introduction_text' => 'TEST_INTRODUCTION_TEXT',
            'is_template_0' => 0,
            'sub_category_id_0' => 0,
            'problem_type_id_0' => 2,
            'difficulty_id_0' => 0,
            'problem_0' => 1,
            'newpage_0' => false,
            'is_template_1' => 0,
            'sub_category_id_1' => 0,
            'problem_type_id_1' => 2,
            'difficulty_id_1' => 0,
            'problem_1' => 0,
            'newpage_1' => false,
            'is_template_2' => -1,
            'sub_category_id_2' => 0,
            'problem_type_id_2' => 0,
            'difficulty_id_2' => 0,
            'problem_2' => 0,
            'newpage_2' => false,
            'is_template_3' => -1,
            'sub_category_id_3' => 0,
            'problem_type_id_3' => 0,
            'difficulty_id_3' => 0,
            'problem_3' => 0,
            'newpage_3' => false,
            'is_template_4' => -1,
            'sub_category_id_4' => 0,
            'problem_type_id_4' => 0,
            'difficulty_id_4' => 0,
            'problem_4' => 0,
            'newpage_4' => false,
            'is_template_5' => -1,
            'sub_category_id_5' => 0,
            'problem_type_id_5' => 0,
            'difficulty_id_5' => 0,
            'problem_5' => 0,
            'newpage_5' => false,
            'is_template_6' => -1,
            'sub_category_id_6' => 0,
            'problem_type_id_6' => 0,
            'difficulty_id_6' => 0,
            'problem_6' => 0,
            'newpage_6' => false,
            'is_template_7' => -1,
            'sub_category_id_7' => 0,
            'problem_type_id_7' => 0,
            'difficulty_id_7' => 0,
            'problem_7' => 0,
            'newpage_7' => false,
            'is_template_8' => -1,
            'sub_category_id_8' => 0,
            'problem_type_id_8' => 0,
            'difficulty_id_8' => 0,
            'problem_8' => 0,
            'newpage_8' => false,
            'is_template_9' => -1,
            'sub_category_id_9' => 0,
            'problem_type_id_9' => 0,
            'difficulty_id_9' => 0,
            'problem_9' => 0,
            'newpage_9' => false,
            'is_template_10' => -1,
            'sub_category_id_10' => 0,
            'problem_type_id_10' => 0,
            'difficulty_id_10' => 0,
            'problem_10' => 0,
            'newpage_10' => false,
            'is_template_11' => -1,
            'sub_category_id_11' => 0,
            'problem_type_id_11' => 0,
            'difficulty_id_11' => 0,
            'problem_11' => 0,
            'newpage_11' => false,
            'is_template_12' => -1,
            'sub_category_id_12' => 0,
            'problem_type_id_12' => 0,
            'difficulty_id_12' => 0,
            'problem_12' => 0,
            'newpage_12' => false,
            'is_template_13' => -1,
            'sub_category_id_13' => 0,
            'problem_type_id_13' => 0,
            'difficulty_id_13' => 0,
            'problem_13' => 0,
            'newpage_13' => false,
            'is_template_14' => -1,
            'sub_category_id_14' => 0,
            'problem_type_id_14' => 0,
            'difficulty_id_14' => 0,
            'problem_14' => 0,
            'newpage_14' => false,
            'is_template_15' => -1,
            'sub_category_id_15' => 0,
            'problem_type_id_15' => 0,
            'difficulty_id_15' => 0,
            'problem_15' => 0,
            'newpage_15' => false,
            'is_template_16' => -1,
            'sub_category_id_16' => 0,
            'problem_type_id_16' => 0,
            'difficulty_id_16' => 0,
            'problem_16' => 0,
            'newpage_16' => false,
            'is_template_17' => -1,
            'sub_category_id_17' => 0,
            'problem_type_id_17' => 0,
            'difficulty_id_17' => 0,
            'problem_17' => 0,
            'newpage_17' => false,
            'is_template_18' => -1,
            'sub_category_id_18' => 0,
            'problem_type_id_18' => 0,
            'difficulty_id_18' => 0,
            'problem_18' => 0,
            'newpage_18' => false,
            'is_template_19' => -1,
            'sub_category_id_19' => 0,
            'problem_type_id_19' => 0,
            'difficulty_id_19' => 0,
            'problem_19' => 0,
            'newpage_19' => false,
        ]);

        // Prepare expected data
        $expectedTest = new Test();
        $expectedTest->setLogo($this->logo);
        $expectedTest->setGroups(new ArrayCollection([$this->group]));
        $expectedTest->setSchoolYear($data->school_year);
        $expectedTest->setTerm($data->test_term);
        $expectedTest->setTestNumber($data->test_number);
        $expectedTest->setIntroductionText($data->introduction_text);
        $expectedTest->setCreated(new DateTime('2000-01-01'));
        $expectedTest->setId(1);

        $aProblemTestAssociation = new ProblemFinalTestVariantAssociation();
        $aProblemTestAssociation->setTest($expectedTest);
        $aProblemTestAssociation->setProblem($this->problemRepositoryMock->find(1));
        $aProblemTestAssociation->setVariant('A');
        $aProblemTestAssociation->setCreated(new DateTime('2000-01-01'));

        $bProblemTestAssociation = new ProblemFinalTestVariantAssociation();
        $bProblemTestAssociation->setTest($expectedTest);
        $bProblemTestAssociation->setProblem($this->problemRepositoryMock->find(1));
        $bProblemTestAssociation->setVariant('B');
        $bProblemTestAssociation->setCreated(new DateTime('2000-01-01'));

        $expectedTest->setProblemAssociations(new ArrayCollection([$aProblemTestAssociation, $bProblemTestAssociation]));

        $expectedRes = ArrayHash::from([
            'testId' => 0,
            'variants' => ['A', 'B'],
            'test' => $expectedTest
        ]);

        // Create first Test entity
        $res = $this->testBuilderService->buildTest($data);

        // Compare real result object to the expected result object
        $this->assertEquals($expectedRes, $res);

        // Update data for Test entity
        $data->problems_cnt = 2;

        // Create second Test entity
        $res = $this->testBuilderService->buildTest($data);

        // Compare result object's values to the expected values
        $this->assertCount(4, $res->test->getProblemAssociations()->getValues());
        $this->assertEquals(1, $res->test->getProblemAssociations()->getValues()[0]->getProblem()->getId());
        $this->assertEquals(1, $res->test->getProblemAssociations()->getValues()[2]->getProblem()->getId());
        $this->assertContains($res->test->getProblemAssociations()->getValues()[1]->getProblem()->getId(), [2, 3, 4, 5]);
        $this->assertContains($res->test->getProblemAssociations()->getValues()[3]->getProblem()->getId(), [2, 3, 4, 5]);

        // Update data for Test entity
        $data->problems_cnt = 1;
        $data->is_template_0 = -1;
        $data->problem_0 = 6;

        // Create third Test entity
        $res = $this->testBuilderService->buildTest($data);

        // Compare result object's values to the expected values
        $this->assertCount(2, $res->test->getProblemAssociations()->getValues());
        $this->assertEquals('TEST_BODY_6_FINAL', $res->test->getProblemAssociations()->getValues()[0]->getProblem()->getBody());
        $this->assertEquals($this->problemRepositoryMock->find(6), $res->test->getProblemAssociations()->getValues()[0]->getProblem()->getProblemTemplate());
        $this->assertEquals($this->problemRepositoryMock->find(6), $res->test->getProblemAssociations()->getValues()[1]->getProblem()->getProblemTemplate());
    }
}
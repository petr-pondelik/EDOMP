<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.6.19
 * Time: 9:59
 */

namespace Tests\Model\Functionality;

use App\Model\Entity\Category;
use App\Model\Entity\Difficulty;
use App\Model\Entity\Group;
use App\Model\Entity\LinearEqTempl;
use App\Model\Entity\Logo;
use App\Model\Entity\ProblemFinal;
use App\Model\Entity\ProblemFinalTestVariantAssociation;
use App\Model\Entity\ProblemType;
use App\Model\Entity\SubCategory;
use App\Model\Entity\SuperGroup;
use App\Model\Entity\Test;
use App\Model\Functionality\ProblemFunctionality;
use App\Model\Repository\ProblemRepository;
use App\Model\Repository\ProblemFinalTestVariantAssociationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ProblemFunctionalityTest
 * @package App\AppTests\Model\Functionality
 */
class ProblemFunctionalityTest extends FunctionalityTestCase
{
    /**
     * @var MockObject
     */
    protected $problemTestAssociationRepositoryMock;

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        // Create ProblemType
        $problemType = new ProblemType();
        $problemType->setLabel('LINEÁRNÍ ROVNICE');
        $problemType->setIsGeneratable(true);
        $problemType->setId(1);

        // Create Difficulty
        $difficulty = new Difficulty();
        $difficulty->setLabel('LEHKÁ');
        $difficulty->setId(1);

        // Create Category
        $category = new Category();
        $category->setLabel('1. ROVNICE');
        $category->setId(1);

        // Create SubCategory
        $subCategory = new SubCategory();
        $subCategory->setLabel('1.1. LINEÁRNÍ ROVNICE');
        $subCategory->setCategory($category);

        // Create ProblemTemplate
        $problemTemplate = new LinearEqTempl();
        $problemTemplate->setProblemType($problemType);
        $problemTemplate->setDifficulty($difficulty);
        $problemTemplate->setSubCategory($subCategory);
        $problemTemplate->setBody('TEST');
        $problemTemplate->setVariable('T');
        $problemTemplate->setId(1);

        // Create first ProblemFinal
        $firstProblemFinal = new ProblemFinal();
        $firstProblemFinal->setProblemType($problemType);
        $firstProblemFinal->setProblemTemplate($problemTemplate);
        $firstProblemFinal->setDifficulty($difficulty);
        $firstProblemFinal->setSubCategory($subCategory);
        $firstProblemFinal->setBody('TEST_FIRST_PROBLEM_FINAL');
        $firstProblemFinal->setVariable('T');
        $firstProblemFinal->setId(2);

        // Create second ProblemFinal
        $secondProblemFinal = new ProblemFinal();
        $secondProblemFinal->setProblemType($problemType);
        $secondProblemFinal->setDifficulty($difficulty);
        $secondProblemFinal->setSubCategory($subCategory);
        $secondProblemFinal->setBody('TEST_SECOND_PROBLEM_FINAL');
        $secondProblemFinal->setVariable('T');
        $secondProblemFinal->setId(3);

        // Create third ProblemFinal
        $thirdProblemFinal = new ProblemFinal();
        $thirdProblemFinal->setProblemType($problemType);
        $thirdProblemFinal->setProblemTemplate($problemTemplate);
        $thirdProblemFinal->setDifficulty($difficulty);
        $thirdProblemFinal->setSubCategory($subCategory);
        $thirdProblemFinal->setBody('TEST_THIRD_PROBLEM_FINAL');
        $thirdProblemFinal->setVariable('T');
        $thirdProblemFinal->setId(4);

        // Create SuperGroup
        $superGroup = new SuperGroup();
        $superGroup->setLabel('TEST_SUPER_GROUP');
        $superGroup->setId(1);

        // Create Group
        $group = new Group();
        $group->setSuperGroup($superGroup);
        $group->setLabel('TEST_GROUP');
        $group->setId(1);

        // Create Logo
        $logo = new Logo();
        $logo->setLabel('TEST_LOGO');
        $logo->setExtensionTmp('TEST_LOGO_EXTENSION_TMP');
        $logo->setExtension('TEST_LOGO_EXTENSION');
        $logo->setPath('TEST_LOGO_PATH');
        $logo->setId(1);

        // Create Test entity
        $test = new Test();
        $test->setGroups(new ArrayCollection([$group]));
        $test->setLogo($logo);
        $test->setSchoolYear('2018/2019');
        $test->setTestNumber(1);
        $test->setTerm('1. pololetí');
        $test->setId(1);

        // Create first ProblemTestAssociation
        $firstProblemTestAssociation = new ProblemFinalTestVariantAssociation();
        $firstProblemTestAssociation->setProblem($firstProblemFinal);
        $firstProblemTestAssociation->setProblemTemplate($problemTemplate);
        $firstProblemTestAssociation->setTest($test);
        $firstProblemTestAssociation->setVariant('A');
        $firstProblemTestAssociation->setSuccessRate(0.5);
        $firstProblemTestAssociation->setId(1);

        // Create second ProblemTestAssociation
        $secondProblemTestAssociation = new ProblemFinalTestVariantAssociation();
        $secondProblemTestAssociation->setProblem($firstProblemFinal);
        $secondProblemTestAssociation->setProblemTemplate($problemTemplate);
        $secondProblemTestAssociation->setTest($test);
        $secondProblemTestAssociation->setVariant('B');
        $secondProblemTestAssociation->setSuccessRate(0.75);
        $secondProblemTestAssociation->setId(2);

        // Create third ProblemTestAssociation
        $thirdProblemTestAssociation = new ProblemFinalTestVariantAssociation();
        $thirdProblemTestAssociation->setProblem($secondProblemFinal);
        $thirdProblemTestAssociation->setTest($test);
        $thirdProblemTestAssociation->setVariant('A');
        $thirdProblemTestAssociation->setSuccessRate(0.25);
        $thirdProblemTestAssociation->setId(3);

        // Create fourth ProblemTestAssociation
        $fourthProblemTestAssociation = new ProblemFinalTestVariantAssociation();
        $fourthProblemTestAssociation->setProblem($secondProblemFinal);
        $fourthProblemTestAssociation->setTest($test);
        $fourthProblemTestAssociation->setVariant('B');
        $fourthProblemTestAssociation->setSuccessRate(0.5);
        $fourthProblemTestAssociation->setId(4);

        // Create fifth ProblemTestAssociation
        $fifthProblemTestAssociation = new ProblemFinalTestVariantAssociation();
        $fifthProblemTestAssociation->setProblem($thirdProblemFinal);
        $fifthProblemTestAssociation->setProblemTemplate($problemTemplate);
        $fifthProblemTestAssociation->setTest($test);
        $fifthProblemTestAssociation->setVariant('B');
        $fifthProblemTestAssociation->setSuccessRate(0.5);
        $fifthProblemTestAssociation->setId(5);

        // Mock the ProblemRepository
        $this->repositoryMock = $this->getMockBuilder(ProblemRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for ProblemRepository
        $this->repositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use (
                $problemTemplate, $firstProblemFinal, $secondProblemFinal, $thirdProblemFinal
            ) {
                $map = [
                    1 => $problemTemplate,
                    2 => $firstProblemFinal,
                    3 => $secondProblemFinal,
                    4 => $thirdProblemFinal,
                    50 => null
                ];
                return $map[$arg];
            });

        // Mock the ProblemTestAssociationRepository
        $this->problemTestAssociationRepositoryMock = $this->getMockBuilder(ProblemFinalTestVariantAssociationRepository::class)
            ->setMethods(['findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for ProblemTestAssociationRepository
        $this->problemTestAssociationRepositoryMock->expects($this->any())
            ->method('findBy')
            ->willReturnCallback(static function ($arg) use (
                $firstProblemTestAssociation, $secondProblemTestAssociation, $thirdProblemTestAssociation,
                $fourthProblemTestAssociation, $fifthProblemTestAssociation
            ) {
                switch ($arg){
                    case ['problemTemplate.id' => 1]:
                        return [$firstProblemTestAssociation, $secondProblemTestAssociation, $fifthProblemTestAssociation];
                    case ['problem.id' => 2]:
                        return [$firstProblemTestAssociation, $secondProblemTestAssociation];
                    case ['problem.id' => 3]:
                        return [$thirdProblemTestAssociation, $fourthProblemTestAssociation];
                    case ['problem.id' => 4]:
                        return [$fifthProblemTestAssociation];
                }
                return null;
            });

        // Instantiate tested class
        $this->functionality = new ProblemFunctionality($this->em, $this->repositoryMock, $this->problemTestAssociationRepositoryMock);
    }

    public function testFunctionality(): void
    {
        // Calculate success rate for ProblemTemplate
        $this->functionality->calculateSuccessRate(1, true);

        // Test expected success rate value
        $problemTemplate = $this->repositoryMock->find(1);
        $this->assertEquals(0.58, $problemTemplate->getSuccessRate());

        // Calculate success rate for first ProblemFinal
        $this->functionality->calculateSuccessRate(2);

        // Test expected success rate value
        $problemFinal = $this->repositoryMock->find(2);
        $this->assertEquals(0.63, $problemFinal->getSuccessRate());

        // Calculate success rate for second ProblemFinal
        $this->functionality->calculateSuccessRate(3);

        // Test expected success rate value
        $problemFinal = $this->repositoryMock->find(3);
        $this->assertEquals(0.38, $problemFinal->getSuccessRate());

        // Calculate success rate for third ProblemFinal
        $this->functionality->calculateSuccessRate(4);

        //Test expected success rate value
        $problemFinal = $this->repositoryMock->find(4);
        $this->assertEquals(0.5, $problemFinal->getSuccessRate());
    }
}
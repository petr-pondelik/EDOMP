<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 9.6.19
 * Time: 16:47
 */

namespace Tests\Model\Functionality;

use App\Model\Entity\Category;
use App\Model\Entity\Difficulty;
use App\Model\Entity\Group;
use App\Model\Entity\Logo;
use App\Model\Entity\ProblemFinal;
use App\Model\Entity\ProblemFinalTestVariantAssociation;
use App\Model\Entity\SubCategory;
use App\Model\Entity\SuperGroup;
use App\Model\Entity\Test;
use App\Model\Functionality\ProblemFinalTestVariantAssociationFunctionality;
use App\Model\Repository\ProblemFinalTestVariantAssociationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;

/**
 * Class ProblemTestAssociationFunctionalityTest
 * @package App\AppTests\Model\Functionality
 */
class ProblemTestAssociationFunctionalityTest extends FunctionalityTestCase
{
    /**
     * @var Test
     */
    protected $test;

    /**
     * @var ProblemFinal
     */
    protected $problemFinal;

    /**
     * @var ProblemFinalTestVariantAssociation
     */
    protected $problemTestAssociation;

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        // Create Logo
        $logo = new Logo();
        $logo->setLabel('TEST_LOGO');
        $logo->setExtensionTmp('TEST_LOGO_EXTENSION_TMP');
        $logo->setExtension('TEST_LOGO_EXTENSION');
        $logo->setPath('TEST_LOGO_PATH');
        $logo->setId(1);


        // Create default SuperGroup
        $superGroup = new SuperGroup();
        $superGroup->setId(1);
        $superGroup->setLabel('TEST_SUPER_GROUP');

        // Create first Group
        $group = new Group();
        $group->setId(1);
        $group->setLabel('TEST_GROUP');
        $group->setSuperGroup($superGroup);

        // Create Test
        $test = new Test();
        $test->setLogo($logo);
        $test->setTerm('TEST_TERM');
        $test->setGroups(new ArrayCollection([$group]));
        $test->setSchoolYear('2018/2019');
        $test->setTestNumber(1);
        $test->setIntroductionText('TEST_INTRODUCTION_TEXT');
        $test->setCreated(new DateTime('2000-01-01'));
        $test->setId(1);
        $this->test = $test;

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
        $subCategory->setId(1);

        // Create ProblemFinal
        $problemFinal = new ProblemFinal();
        $problemFinal->setBody('TEST_BODY_FIRST');
        $problemFinal->setDifficulty($difficulty);
        $problemFinal->setSubCategory($subCategory);
        $problemFinal->setId(1);
        $this->problemFinal = $problemFinal;

        // Create ProblemTestAssociation
        $problemTestAssociation = new ProblemFinalTestVariantAssociation();
        $problemTestAssociation->setProblem($problemFinal);
        $problemTestAssociation->setTest($test);
        $problemTestAssociation->setVariant('A');
        $problemTestAssociation->setId(1);
        $this->problemTestAssociation = $problemTestAssociation;

        // Mock the ProblemTestAssociationRepository
        $this->repositoryMock = $this->getMockBuilder(ProblemFinalTestVariantAssociationRepository::class)
            ->setMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        //TODO: Set expected return values for findOneBy method

        // Set expected return values for ProblemTestAssociationRepository findOneBy method
        $this->repositoryMock->expects($this->any())
            ->method('findOneBy')
            ->willReturnCallback(static function ($arg) use ($problemTestAssociation) {
                switch ($arg){
                    case [
                        'problem.id' => 1,
                        'test.id' => 1
                    ]:
                        return $problemTestAssociation;
                    default:
                        return null;
                }
            });

        // Instantiate tested class
        $this->functionality = new ProblemFinalTestVariantAssociationFunctionality($this->em, $this->repositoryMock);
    }

    /**
     * @throws \Exception
     */
    public function testFunctionality(): void
    {
        // Data for ProblemTestAssociation update
        $data = ArrayHash::from([
            'test_id' => 1,
            'success_rate' => 0.5
        ]);

        // Prepare expected ProblemTestAssociation object
        $problemTestAssociationExpected = new ProblemFinalTestVariantAssociation();
        $problemTestAssociationExpected->setProblem($this->problemFinal);
        $problemTestAssociationExpected->setTest($this->test);
        $problemTestAssociationExpected->setVariant('A');
        $problemTestAssociationExpected->setSuccessRate(0.5);
        $problemTestAssociationExpected->setId(1);

        // Update ProblemTestAssociation
        $problemTestAssociation = $this->functionality->update(1, $data);

        //Compare updated ProblemTestAssociation with expected values
        $this->assertEquals($problemTestAssociationExpected->getProblem(), $problemTestAssociation->getProblem());
        $this->assertEquals($problemTestAssociationExpected->getTest(), $problemTestAssociation->getTest());
        $this->assertEquals($problemTestAssociationExpected->getVariant(), $problemTestAssociation->getVariant());
        $this->assertEquals($problemTestAssociationExpected->getSuccessRate(), $data->success_rate);

        // Data for ProblemTestAssociation update
        $data = ArrayHash::from([
            'test_id' => 1,
            'success_rate' => null
        ]);

        // Prepare expected ProblemTestAssociation object
        $problemTestAssociationExpected->setSuccessRate($data->success_rate);

        // Update ProblemTestAssociation
        $problemTestAssociation = $this->functionality->update(1, $data);

        //Compare updated ProblemTestAssociation with expected values
        $this->assertEquals($problemTestAssociationExpected->getProblem(), $problemTestAssociation->getProblem());
        $this->assertEquals($problemTestAssociationExpected->getTest(), $problemTestAssociation->getTest());
        $this->assertEquals($problemTestAssociationExpected->getVariant(), $problemTestAssociation->getVariant());
        $this->assertEquals($problemTestAssociationExpected->getSuccessRate(), $data->success_rate);
    }
}
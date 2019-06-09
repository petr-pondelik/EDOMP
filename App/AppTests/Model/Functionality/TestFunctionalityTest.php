<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 9.6.19
 * Time: 14:53
 */

namespace App\AppTests\Model\Functionality;

use App\Model\Entity\Group;
use App\Model\Entity\Logo;
use App\Model\Entity\SuperGroup;
use App\Model\Entity\Test;
use App\Model\Functionality\TestFunctionality;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\LogoRepository;
use App\Model\Repository\TestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class TestFunctionalityTest
 * @package App\AppTests\Model\Functionality
 */
class TestFunctionalityTest extends FunctionalityTestCase
{
    /**
     * @var MockObject
     */
    protected $logoRepositoryMock;

    /**
     * @var MockObject
     */
    protected $groupRepositoryMock;

    /**
     * @throws \ReflectionException
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
        $superGroup->setLabel('TEST_SUPER_GROUP_DEFAULT');

        // Create first Group
        $firstGroup = new Group();
        $firstGroup->setId(1);
        $firstGroup->setLabel('TEST_FIRST_GROUP');
        $firstGroup->setSuperGroup($superGroup);

        // Create second Group
        $secondGroup = new Group();
        $secondGroup->setId(2);
        $secondGroup->setLabel('TEST_SECOND_GROUP');
        $secondGroup->setSuperGroup($superGroup);

        // Mock the TestRepository
        $this->repositoryMock = $this->getMockBuilder(TestRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Mock the LogoRepository
        $this->logoRepositoryMock = $this->getMockBuilder(LogoRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for LogoRepository find method
        $this->logoRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use (
                $logo
            ) {
                switch ($arg){
                    case 1: return $logo;
                    default: return null;
                }
            });

        // Mock the GroupRepository
        $this->groupRepositoryMock = $this->getMockBuilder(GroupRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for GroupRepository find method
        $this->groupRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use (
                $firstGroup, $secondGroup
            ) {
                switch ($arg){
                    case 1: return $firstGroup;
                    case 2: return $secondGroup;
                    default: return null;
                }
            });

        // Instantiate tested class
        $this->functionality = new TestFunctionality($this->em, $this->repositoryMock, $this->logoRepositoryMock, $this->groupRepositoryMock);
    }

    /**
     * @throws \Exception
     */
    public function testFunctionality(): void
    {
        // Data for Test create
        $data = ArrayHash::from([
            'logo_id' => 1,
            'term' => 'TEST_TERM',
            'groups' => [1, 2],
            'school_year' => '2018/2019',
            'test_number' => 1,
            'introduction_text' => 'TEST_INTRODUCTION_TEST',
            'created' => new DateTime('2000-01-01')
        ]);

        // Prepare Test expected object
        $testExpected = new Test();
        $testExpected->setLogo($this->logoRepositoryMock->find($data->logo_id));
        $testExpected->setTerm($data->term);
        $testExpected->setGroups(new ArrayCollection([
            $this->groupRepositoryMock->find($data->groups[0]),
            $this->groupRepositoryMock->find($data->groups[1])
        ]));
        $testExpected->setSchoolYear($data->school_year);
        $testExpected->setTestNumber($data->test_number);
        $testExpected->setIntroductionText($data->introduction_text);
        $testExpected->setCreated($data->created);

        // Create Test
        $test = $this->functionality->create($data);

        // Test created Test against expected object
        $this->assertEquals($testExpected, $test);
    }
}
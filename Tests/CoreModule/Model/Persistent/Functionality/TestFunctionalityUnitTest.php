<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.12.19
 * Time: 12:22
 */

namespace App\Tests\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\Test;
use App\CoreModule\Model\Persistent\Functionality\ProblemFinalTestVariantAssociationFunctionality;
use App\CoreModule\Model\Persistent\Functionality\ProblemFunctionality;
use App\CoreModule\Model\Persistent\Functionality\TestFunctionality;
use App\Tests\MockTraits\Repository\GroupRepositoryMockTrait;
use App\Tests\MockTraits\Repository\LogoRepositoryMockTrait;
use App\Tests\MockTraits\Repository\TestRepositoryMockTrait;
use App\Tests\MockTraits\Repository\TestVariantRepositoryMockTrait;
use App\Tests\MockTraits\Repository\UserRepositoryMockTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class TestFunctionalityUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Functionality
 */
final class TestFunctionalityUnitTest extends FunctionalityUnitTestCase
{
    use TestRepositoryMockTrait;
    use LogoRepositoryMockTrait;
    use GroupRepositoryMockTrait;
    use UserRepositoryMockTrait;
    use TestVariantRepositoryMockTrait;

    /**
     * @var TestFunctionality
     */
    protected $functionality;

    /**
     * @var MockObject
     */
    protected $problemFinalTestVariantAssociationFunctionalityMock;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Traits setUp
        $this->setUpTestRepositoryMock();
        $this->setUpLogoRepositoryMock();
        $this->setUpGroupRepositoryMock();
        $this->setUpUserRepositoryMock();
        $this->setUpTestVariantRepositoryMock();

        // SetUp functionalities
        $this->problemFinalTestVariantAssociationFunctionalityMock = $this->getMockBuilder(ProblemFinalTestVariantAssociationFunctionality::class)
                                                                    ->disableOriginalConstructor()
                                                                    ->getMock();
        $problemFunctionality = $this->getMockBuilder(ProblemFunctionality::class)
                                    ->disableOriginalConstructor()
                                    ->getMock();
        $this->functionality = new TestFunctionality(
            $this->em, $this->testRepositoryMock, $this->logoRepositoryMock, $this->groupRepositoryMock,
            $this->userRepositoryMock, $this->problemFinalTestVariantAssociationFunctionalityMock, $problemFunctionality
        );

        $this->firstTest->addTestVariant($this->firstTestVariant);
    }

    /**
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function testCreate(): void
    {
        // Data for test create
        $data = [
            'logo' => 1,
            'term' => 'TEST_TERM',
            'schoolYear' => '2019/2020',
            'testNumber' => 1,
            'groups' => [1, 2],
            'introductionText' => 'TEST_INTRODUCTION_TEXT',
            'variantsCnt' => 2,
            'problemsPerVariant' => 5,
            'userId' => 1,
            'created' => DateTime::from($this->dateTimeStr)
        ];

        // Prepare Test expected object
        $expected = new Test();
        $expected->setLogo($this->logoRepositoryMock->find($data['logo']));
        $expected->setTerm($data['term']);
        $expected->setGroups(new ArrayCollection([
            $this->groupRepositoryMock->find($data['groups'][0]),
            $this->groupRepositoryMock->find($data['groups'][1])
        ]));
        $expected->setSchoolYear($data['schoolYear']);
        $expected->setTestNumber($data['testNumber']);
        $expected->setIntroductionText($data['introductionText']);
        $expected->setVariantsCnt($data['variantsCnt']);
        $expected->setProblemsPerVariant($data['problemsPerVariant']);
        $expected->setCreatedBy($this->userRepositoryMock->find($data['userId']));
        $expected->setCreated($data['created']);

        // Create Test
        $created = $this->functionality->create($data);

        // Test created Test against expected object
        $this->assertEquals($expected, $created);
    }

    /**
     * @throws EntityNotFoundException
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function testCreateLogoFail(): void
    {
        // Data for test create
        $data = [
            'logo' => 100,
            'term' => 'TEST_TERM',
            'schoolYear' => '2019/2020',
            'testNumber' => 1,
            'groups' => [1, 2],
            'introductionText' => 'TEST_INTRODUCTION_TEXT',
            'variantsCnt' => 2,
            'problemsPerVariant' => 5,
            'userId' => 1,
            'created' => DateTime::from($this->dateTimeStr)
        ];

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Logo not found.');
        $this->functionality->create($data);
    }

    /**
     * @throws EntityNotFoundException
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function testCreateUserFail(): void
    {
        // Data for test create
        $data = [
            'logo' => 1,
            'term' => 'TEST_TERM',
            'schoolYear' => '2019/2020',
            'testNumber' => 1,
            'groups' => [1, 2],
            'introductionText' => 'TEST_INTRODUCTION_TEXT',
            'variantsCnt' => 2,
            'problemsPerVariant' => 5,
            'userId' => 100,
            'created' => DateTime::from($this->dateTimeStr)
        ];

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('User not found.');
        $this->functionality->create($data);
    }

    /**
     * @throws EntityNotFoundException
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function testCreateGroupFail(): void
    {
        // Data for test create
        $data = [
            'logo' => 1,
            'term' => 'TEST_TERM',
            'schoolYear' => '2019/2020',
            'testNumber' => 1,
            'groups' => [100, 2],
            'introductionText' => 'TEST_INTRODUCTION_TEXT',
            'variantsCnt' => 2,
            'problemsPerVariant' => 5,
            'userId' => 1,
            'created' => DateTime::from($this->dateTimeStr)
        ];

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Group not found.');
        $this->functionality->create($data);
    }

    /**
     * @throws EntityNotFoundException
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function testUpdate(): void
    {
        // Data for test update
        $data = [
            'updateBasics' => true,
            'updateStatistics' => false,
            'logo' => 1,
            'term' => 'TEST_TERM',
            'schoolYear' => '2019/2020',
            'testNumber' => 1,
            'groups' => [1, 2],
            'introductionText' => 'TEST_INTRODUCTION_TEXT',
            'variantsCnt' => 2,
            'problemsPerVariant' => 5,
            'userId' => 1,
            'created' => DateTime::from($this->dateTimeStr)
        ];

        // Prepare Test expected object
        $expected = $this->firstTest;
        $expected->setLogo($this->logoRepositoryMock->find($data['logo']));
        $expected->setTerm($data['term']);
        $expected->setGroups(new ArrayCollection([
            $this->groupRepositoryMock->find($data['groups'][0]),
            $this->groupRepositoryMock->find($data['groups'][1])
        ]));
        $expected->setSchoolYear($data['schoolYear']);
        $expected->setTestNumber($data['testNumber']);
        $expected->setIntroductionText($data['introductionText']);
        $expected->setVariantsCnt($data['variantsCnt']);
        $expected->setProblemsPerVariant($data['problemsPerVariant']);
        $expected->setCreatedBy($this->userRepositoryMock->find($data['userId']));
        $expected->setCreated($data['created']);

        // Update Test
        $updated = $this->functionality->update(1, $data);

        // Assert updated Test against expected object
        $this->assertEquals($expected, $updated);
    }

    /**
     * @throws EntityNotFoundException
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function testUpdateTestStatisticsSimple(): void
    {
        // Data for test update
        $data = [
            'updateBasics' => false,
            'updateStatistics' => true,
            'problemFinalId00' => 1,
            'successRate00' => 0.9
        ];

        // Expect to call update method exactly once
        $this->problemFinalTestVariantAssociationFunctionalityMock->expects($this->once())
            ->method('update');

        // Perform update
        $this->functionality->update(1, $data);
    }

    /**
     * @throws EntityNotFoundException
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function testUpdateTestStatisticsAdvanced(): void
    {
        // Test case with more variants and more problems per variant
        $this->firstTest->setVariantsCnt(2);
        $this->firstTest->setProblemsPerVariant(2);

        // Add TestVariant to Test
        $this->firstTest->addTestVariant($this->secondTestVariant);

        // Data for test update
        $data = [
            'updateBasics' => false,
            'updateStatistics' => true,
            'problemFinalId00' => 1,
            'problemFinalId01' => 2,
            'problemFinalId10' => 1,
            'problemFinalId11' => 2,
            'successRate00' => 0.9,
            'successRate01' => 0.8,
            'successRate10' => 0.7,
            'successRate11' => 0.6,
        ];

        // Expect to call update method exactly four-times
        $this->problemFinalTestVariantAssociationFunctionalityMock->expects($this->exactly(4))
            ->method('update');

        // Perform update
        $this->functionality->update(1, $data);
    }
}
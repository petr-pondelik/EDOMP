<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 9.6.19
 * Time: 11:40
 */

namespace App\AppTests\Model\Functionality;

use App\Model\Entity\TemplateJsonData;
use App\Model\Functionality\TemplateJsonDataFunctionality;
use App\Model\Repository\ProblemTemplateRepository;
use App\Model\Repository\TemplateJsonDataRepository;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class TemplateJsonDataFunctionalityTest
 * @package App\AppTests\Model\Functionality
 */
class TemplateJsonDataFunctionalityTest extends FunctionalityTestCase
{
    /**
     * @var MockObject
     */
    protected $problemTemplateRepositoryMock;

    /**
     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        parent::setUp();

        // Mock the TemplateJsonDataRepository
        $this->repositoryMock = $this->getMockBuilder(TemplateJsonDataRepository::class)
            ->setMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        // Mock the ProblemTemplateRepository
        $this->problemTemplateRepositoryMock = $this->getMockBuilder(ProblemTemplateRepository::class)
            ->setMethods(['getSequenceVal'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return value for ProblemTemplateRepository getSequenceVal method
        $this->problemTemplateRepositoryMock->expects($this->any())
            ->method('getSequenceVal')
            ->willReturn(1);

        // Instantiate tested class
        $this->functionality = new TemplateJsonDataFunctionality($this->em, $this->repositoryMock, $this->problemTemplateRepositoryMock);
    }

    /**
     * @throws \Exception
     */
    public function testFunctionality(): void
    {
        // Data for TemplateJsonData create
        $data = ArrayHash::from([
            'jsonData' => '{matches: [{"p0": 11}]}',
            'created' => new DateTime('2000-01-01')
        ]);

        // Prepare TemplateJsonData expected object
        $templateJsonDataExpected = new TemplateJsonData();
        $templateJsonDataExpected->setJsonData($data->jsonData);
        $templateJsonDataExpected->setTemplateId($this->problemTemplateRepositoryMock->getSequenceVal());
        $templateJsonDataExpected->setCreated($data->created);

        // Create TemplateJsonData
        $templateJsonData = $this->functionality->create($data);

        // Test created TemplateJsonData against expected values
        $this->assertEquals($templateJsonDataExpected, $templateJsonData);

        // Set expected return value for TemplateJsonDataRepository method findOneBy
        $this->repositoryMock->expects($this->any())
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

        // Data for TemplateJsonData update
        $data = ArrayHash::from([
            'jsonData' => '{matches: [{"p0": 22}]}'
        ]);

        // Prepare expected TemplateJsonData object
        $templateJsonDataExpected->setJsonData($data->jsonData);

        // Update TemplateJsonData
        $templateJsonData = $this->functionality->create($data, 1);

        // Test updated TemplateJsonData against expected values
        $this->assertEquals($templateJsonDataExpected, $templateJsonData);
    }
}
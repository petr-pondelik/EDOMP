<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.6.19
 * Time: 17:03
 */

namespace App\AppTests\Model\Functionality;

use App\Model\Entity\ProblemType;
use App\Model\Functionality\ProblemTypeFunctionality;
use App\Model\Repository\ProblemTypeRepository;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemTypeFunctionalityTest
 * @package App\AppTests\Model\Functionality
 */
class ProblemTypeFunctionalityTest extends FunctionalityTestCase
{
    /**
     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        parent::setUp();

        // Mock the ProblemTypeRepository
        $this->repositoryMock = $this->getMockBuilder(ProblemTypeRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        //Instantiate tested class
        $this->functionality = new ProblemTypeFunctionality($this->em, $this->repositoryMock);
    }

    /**
     * @throws \Exception
     */
    public function testFunctionality(): void
    {
        // Data for Category create
        $data = ArrayHash::from([
            'label' => 'TEST_PROBLEM_TYPE'
        ]);

        // Create ProblemType and test expected data
        $problemType = $this->functionality->create($data);
        $this->assertInstanceOf(ProblemType::class, $problemType);
        $this->assertEquals($data->label, $problemType->getLabel());

        // Set repository expected return values for find
        $this->repositoryMock->expects($this->atLeastOnce())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($problemType) {
                $map = [
                    1 => $problemType,
                    50 => null
                ];
                return $map[$arg];
            });

        // Data for ProblemType update
        $data = ArrayHash::from([
            'label' => 'NEW_TEST_PROBLEM_TYPE'
        ]);

        // Update ProblemType and test expected data
        $problemType = $this->functionality->update(1, $data);
        $this->assertInstanceOf(ProblemType::class, $problemType);
        $this->assertEquals( $problemType->getLabel(), $data->label);

        // Try to delete, success expected
        $this->assertEquals(true, $this->functionality->delete(1));

        // Try to delete, exception expected
        $this->expectException(EntityNotFoundException::class);
        $this->functionality->delete(50);
    }
}
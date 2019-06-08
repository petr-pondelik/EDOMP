<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.6.19
 * Time: 17:06
 */

namespace App\AppTests\Model\Functionality;


use App\Model\Entity\GeometricSeqTempl;
use App\Model\Functionality\GeometricSeqTemplFunctionality;
use App\Model\Repository\GeometricSeqTemplRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;

/**
 * Class GeometricSeqTemplFunctionalityTest
 * @package App\AppTests\Model\Functionality
 */
class GeometricSeqTemplFunctionalityTest extends ProblemTemplateFunctionalityTestCase
{
    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        // Mock the GeometricSeqTemplRepository
        $this->repositoryMock = $this->getMockBuilder(GeometricSeqTemplRepository::class)
            ->setMethods(['find', 'getSequenceVal'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for GeometricSeqTemplRepository getSequenceVal method
        $this->repositoryMock->expects($this->any())
            ->method('getSequenceVal')
            ->willReturn(1);

        // Instantiate tested class
        $this->functionality = new GeometricSeqTemplFunctionality(
            $this->em, $this->repositoryMock, $this->problemTypeRepositoryMock, $this->problemConditionRepositoryMock,
            $this->difficultyRepositoryMock, $this->subCategoryRepositoryMock, $this->templateJsonDataRepositoryMock
        );
    }

    /**
     * @throws \Exception
     */
    public function testFunctionality(): void
    {
        // Data for ArithmeticSeqTempl create
        $data = ArrayHash::from([
            'variable' => 'T',
            'body' => 'TEST_BODY',
            'text_before' => 'TEST_TEXT_BEFORE',
            'text_after' => 'TEST_TEXT_AFTER',
            'first_n' => 5,
            'type' => 1,
            'difficulty' => 1,
            'subcategory' => 1,
            'condition_1' => 0,
            'condition_2' => 0,
            'created' => new DateTime('2000-01-01')
        ]);

        // Prepare GeometricSeqTempl expected object
        $geometricSeqTemplExpected = new GeometricSeqTempl();
        $geometricSeqTemplExpected->setVariable($data->variable);
        $geometricSeqTemplExpected->setBody($data->body);
        $geometricSeqTemplExpected->setTextBefore($data->text_before);
        $geometricSeqTemplExpected->setTextAfter($data->text_after);
        $geometricSeqTemplExpected->setFirstN($data->first_n);
        $geometricSeqTemplExpected->setProblemType($this->problemTypeRepositoryMock->find($data->type));
        $geometricSeqTemplExpected->setDifficulty($this->difficultyRepositoryMock->find($data->difficulty));
        $geometricSeqTemplExpected->setSubCategory($this->subCategoryRepositoryMock->find($data->subcategory));
        $geometricSeqTemplExpected->setConditions(new ArrayCollection([
                $this->problemConditionRepositoryMock->findOneBy([
                    'problemConditionType.id' => 1,
                    'accessor' => $data->condition_1
                ]),
                $this->problemConditionRepositoryMock->findOneBy([
                    'problemConditionType.id' => 2,
                    'accessor' => $data->condition_2
                ])
            ])
        );
        $geometricSeqTemplExpected->setCreated($data->created);

        // Create GeometricSeqTempl
        $geometricSeqTempl = $this->functionality->create($data);

        // Test created GeometricSeqTempl against expected GeometricSeqTempl object
        $this->assertEquals($geometricSeqTemplExpected, $geometricSeqTempl);

        // Data for arithmeticSeqTempl update
        $data = ArrayHash::from([
            'variable' => 'U',
            'body' => 'TEST_BODY_NEW',
            'text_before' => 'TEST_TEXT_BEFORE_NEW',
            'text_after' => 'TEST_TEXT_AFTER_NEW',
            'first_n' => 10,
            'type' => 2,
            'difficulty' => 2,
            'subcategory' => 2,
            'matches' => '[]',
            'condition_1' => 1,
            'condition_2' => 1,
            'created' => new DateTime('2000-02-02')
        ]);

        // Prepare updated GeometricSeqTempl expected object
        $geometricSeqTemplExpected->setVariable($data->variable);
        $geometricSeqTemplExpected->setBody($data->body);
        $geometricSeqTemplExpected->setTextBefore($data->text_before);
        $geometricSeqTemplExpected->setTextAfter($data->text_after);
        $geometricSeqTemplExpected->setFirstN($data->first_n);
        $geometricSeqTemplExpected->setProblemType($this->problemTypeRepositoryMock->find($data->type));
        $geometricSeqTemplExpected->setDifficulty($this->difficultyRepositoryMock->find($data->difficulty));
        $geometricSeqTemplExpected->setSubCategory($this->subCategoryRepositoryMock->find($data->subcategory));
        $geometricSeqTemplExpected->setMatches($data->matches);
        $geometricSeqTemplExpected->setConditions(new ArrayCollection([
                $this->problemConditionRepositoryMock->findOneBy([
                    'problemConditionType.id' => 1,
                    'accessor' => $data->condition_1
                ]),
                $this->problemConditionRepositoryMock->findOneBy([
                    'problemConditionType.id' => 2,
                    'accessor' => $data->condition_2
                ])
            ])
        );
        $geometricSeqTemplExpected->setCreated($data->created);

        // Set expected return values for GeometricSeqTemplRepository find method
        $this->repositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use (
                $geometricSeqTempl
            ) {
                switch ($arg){
                    case 1:
                        return $geometricSeqTempl;
                }
                return false;
            });

        // Update GeometricSeqTempl
        $geometricSeqTempl = $this->functionality->update(1, $data);

        // Test updated GeometricSeqTempl against expected GeometricSeqTempl object
        $this->assertEquals($geometricSeqTemplExpected, $geometricSeqTempl);
    }
}
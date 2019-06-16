<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.6.19
 * Time: 16:49
 */

namespace Tests\Model\Functionality;

use App\Model\Entity\ArithmeticSeqTempl;
use App\Model\Functionality\ArithmeticSeqTemplFunctionality;
use App\Model\Repository\ArithmeticSeqTemplRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;

/**
 * Class ArithmeticSeqTemplFunctionalityTest
 * @package App\AppTests\Model\Functionality
 */
class ArithmeticSeqTemplFunctionalityTest extends ProblemFunctionalityTestCase
{
    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        // Mock the ArithmeticSeqTemplRepository
        $this->repositoryMock = $this->getMockBuilder(ArithmeticSeqTemplRepository::class)
            ->setMethods(['find', 'getSequenceVal'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for ArithmeticSeqTemplRepository getSequenceVal method
        $this->repositoryMock->expects($this->any())
            ->method('getSequenceVal')
            ->willReturn(1);

        // Instantiate tested class
        $this->functionality = new ArithmeticSeqTemplFunctionality(
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

        // Prepare ArithmeticSeqTempl expected object
        $arithmeticSeqTemplExpected = new ArithmeticSeqTempl();
        $arithmeticSeqTemplExpected->setVariable($data->variable);
        $arithmeticSeqTemplExpected->setBody($data->body);
        $arithmeticSeqTemplExpected->setTextBefore($data->text_before);
        $arithmeticSeqTemplExpected->setTextAfter($data->text_after);
        $arithmeticSeqTemplExpected->setFirstN($data->first_n);
        $arithmeticSeqTemplExpected->setProblemType($this->problemTypeRepositoryMock->find($data->type));
        $arithmeticSeqTemplExpected->setDifficulty($this->difficultyRepositoryMock->find($data->difficulty));
        $arithmeticSeqTemplExpected->setSubCategory($this->subCategoryRepositoryMock->find($data->subcategory));
        $arithmeticSeqTemplExpected->setConditions(new ArrayCollection([
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
        $arithmeticSeqTemplExpected->setCreated($data->created);

        // Create ArithmeticSeqTempl
        $arithmeticSeqTempl = $this->functionality->create($data);

        // Test created ArithmeticSeqTempl against expected ArithmeticSeqTempl object
        $this->assertEquals($arithmeticSeqTemplExpected, $arithmeticSeqTempl);

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

        // Prepare updated ArithmeticSeqTempl expected object
        $arithmeticSeqTemplExpected->setVariable($data->variable);
        $arithmeticSeqTemplExpected->setBody($data->body);
        $arithmeticSeqTemplExpected->setTextBefore($data->text_before);
        $arithmeticSeqTemplExpected->setTextAfter($data->text_after);
        $arithmeticSeqTemplExpected->setFirstN($data->first_n);
        $arithmeticSeqTemplExpected->setProblemType($this->problemTypeRepositoryMock->find($data->type));
        $arithmeticSeqTemplExpected->setDifficulty($this->difficultyRepositoryMock->find($data->difficulty));
        $arithmeticSeqTemplExpected->setSubCategory($this->subCategoryRepositoryMock->find($data->subcategory));
        $arithmeticSeqTemplExpected->setMatches($data->matches);
        $arithmeticSeqTemplExpected->setConditions(new ArrayCollection([
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
        $arithmeticSeqTemplExpected->setCreated($data->created);

        // Set expected return values for ArithmeticSeqTemplRepository find method
        $this->repositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use (
                $arithmeticSeqTempl
            ) {
                switch ($arg){
                    case 1:
                        return $arithmeticSeqTempl;
                }
                return false;
            });

        // Update ArithmeticSeqTempl
        $arithmeticSeqTempl = $this->functionality->update(1, $data);

        // Test updated ArithmeticSeqTempl against expected ArithmeticSeqTempl object
        $this->assertEquals($arithmeticSeqTemplExpected, $arithmeticSeqTempl);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.6.19
 * Time: 17:36
 */

namespace Tests\Model\Functionality;


use App\Helpers\FormatterHelper;
use App\Model\Entity\ProblemFinal;
use App\Model\Functionality\ProblemFinalFunctionality;
use App\Model\Repository\ProblemFinalRepository;
use App\Model\Repository\ProblemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ProblemFinalFunctionalityTest
 * @package App\AppTests\Model\Functionality
 */
class ProblemFinalFunctionalityTest extends ProblemFunctionalityTestCase
{
    /**
     * @var MockObject
     */
    protected $formatterHelperMock;

    /**
     * @var MockObject
     */
    protected $problemRepositoryMock;

    /**
     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        parent::setUp();

        // Mock the ProblemFinalRepository
        $this->repositoryMock = $this->getMockBuilder(ProblemFinalRepository::class)
            ->setMethods(['find', 'getSequenceVal'])
            ->disableOriginalConstructor()
            ->getMock();

        // Mock the ProblemFinalRepository
        $this->problemRepositoryMock = $this->getMockBuilder(ProblemRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Mock the FormatterHelper
        $this->formatterHelperMock = $this->getMockBuilder(FormatterHelper::class)
            ->setMethods(['formatResult'])
            ->disableOriginalConstructor()
            ->getMock();

        // Instantiate tested class
        $this->functionality = new ProblemFinalFunctionality(
            $this->em, $this->repositoryMock, $this->problemRepositoryMock, $this->problemTypeRepositoryMock,
            $this->problemConditionRepositoryMock, $this->difficultyRepositoryMock, $this->subCategoryRepositoryMock,
            $this->formatterHelperMock
        );
    }

    /**
     * @throws \Exception
     */
    public function testFunctionality(): void
    {
        // Data for ProblemFinal create
        $data = ArrayHash::from([
            'variable' => 'T',
            'body' => 'TEST_BODY',
            'text_before' => 'TEST_TEXT_BEFORE',
            'text_after' => 'TEST_TEXT_AFTER',
            'problemFinalType' => 1,
            'difficulty' => 1,
            'subCategory' => 1,
            'condition_1' => 0,
            'condition_2' => 0,
            'created' => new DateTime('2000-01-01')
        ]);

        // Prepare ProblemFinal expected object
        $problemFinalExpected = new ProblemFinal();
        $problemFinalExpected->setVariable($data->variable);
        $problemFinalExpected->setBody($data->body);
        $problemFinalExpected->setTextBefore($data->text_before);
        $problemFinalExpected->setTextAfter($data->text_after);
        $problemFinalExpected->setProblemType($this->problemTypeRepositoryMock->find($data->problemFinalType));
        $problemFinalExpected->setDifficulty($this->difficultyRepositoryMock->find($data->difficulty));
        $problemFinalExpected->setSubCategory($this->subCategoryRepositoryMock->find($data->subCategory));
        $problemFinalExpected->setConditions(new ArrayCollection([
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
        $problemFinalExpected->setCreated($data->created);

        // Create ProblemFinal
        $problemFinal = $this->functionality->create($data);

        // Test created ProblemFinal against expected ProblemFinal object
        $this->assertEquals($problemFinalExpected, $problemFinal);

        // Data for ProblemFinal update
        $data = ArrayHash::from([
            'body' => '15x + 10x - 5 = 0',
            'text_before' => 'TEST_TEXT_BEFORE_NEW',
            'text_after' => 'TEST_TEXT_AFTER_NEW',
            'problemFinalType' => 2,
            'difficulty' => 2,
            'subCategory' => 2,
            'condition_1' => 1,
            'condition_2' => 1,
            'created' => new DateTime('2000-01-01')
        ]);

        // Prepare updated ProblemFinal expected object
        $problemFinalExpected->setBody($data->body);
        $problemFinalExpected->setTextBefore($data->text_before);
        $problemFinalExpected->setTextAfter($data->text_after);
        $problemFinalExpected->setProblemType($this->problemTypeRepositoryMock->find($data->problemFinalType));
        $problemFinalExpected->setDifficulty($this->difficultyRepositoryMock->find($data->difficulty));
        $problemFinalExpected->setSubCategory($this->subCategoryRepositoryMock->find($data->subCategory));
        $problemFinalExpected->setConditions(new ArrayCollection([
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
        $problemFinalExpected->setCreated($data->created);

        // Set expected return values for ProblemFinalRepository find method
        $this->repositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use (
                $problemFinal
            ) {
                switch ($arg){
                    case 1:
                        return $problemFinal;
                }
                return false;
            });

        // Set expected return values for FormatterHelper formatResult method
        $this->formatterHelperMock->expects($this->any())
            ->method('formatResult')
            ->with( ArrayHash::from(['x' => 0.2]) )
            ->willReturn('$$ x = 0.2 $$');

        // Update ProblemFinal
        $problemFinal = $this->functionality->update(1, $data);

        // Test updated ProblemFinal against expected ProblemFinal object
        $this->assertEquals($problemFinalExpected, $problemFinal);

        // Store result for the ProblemFinal entity
        $this->functionality->storeResult(1, ArrayHash::from([
            'x' => 0.2
        ]));

        // Test expected ProblemFinal's result format
        $this->assertEquals('$$ x = 0.2 $$', $problemFinal->getResult());
    }
}
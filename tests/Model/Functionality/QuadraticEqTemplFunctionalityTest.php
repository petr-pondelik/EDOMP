<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.6.19
 * Time: 16:33
 */

namespace Tests\Model\Functionality;

use App\Model\Entity\QuadraticEqTempl;
use App\Model\Functionality\QuadraticEqTemplFunctionality;
use App\Model\Repository\QuadraticEqTemplRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;

/**
 * Class QuadraticEqFunctionalityTest
 * @package App\AppTests\Model\Functionality
 */
class QuadraticEqTemplFunctionalityTest extends ProblemFunctionalityTestCase
{
    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        // Mock the QuadraticEqTemplRepository
        $this->repositoryMock = $this->getMockBuilder(QuadraticEqTemplRepository::class)
            ->setMethods(['find', 'getSequenceVal'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for QuadraticEqTemplRepository getSequenceVal method
        $this->repositoryMock->expects($this->any())
            ->method('getSequenceVal')
            ->willReturn(1);

        // Instantiate tested class
        $this->functionality = new QuadraticEqTemplFunctionality(
            $this->em, $this->repositoryMock, $this->problemTypeRepositoryMock, $this->problemConditionRepositoryMock,
            $this->difficultyRepositoryMock, $this->subCategoryRepositoryMock, $this->templateJsonDataRepositoryMock
        );
    }

    /**
     * @throws \Exception
     */
    public function testFunctionality(): void
    {
        // Data for QuadraticEqTempl create
        $data = ArrayHash::from([
            'variable' => 'T',
            'body' => 'TEST_BODY',
            'textBefore' => 'TEST_textBefore',
            'textAfter' => 'TEST_TEXT_AFTER',
            'type' => 1,
            'difficulty' => 1,
            'subCategory' => 1,
            'condition_1' => 0,
            'condition_2' => 0,
            'created' => new DateTime('2000-01-01')
        ]);

        // Prepare QuadraticEqTempl expected object
        $quadraticEqTemplExpected = new QuadraticEqTempl();
        $quadraticEqTemplExpected->setVariable($data->variable);
        $quadraticEqTemplExpected->setBody($data->body);
        $quadraticEqTemplExpected->setTextBefore($data->textBefore);
        $quadraticEqTemplExpected->setTextAfter($data->textAfter);
        $quadraticEqTemplExpected->setProblemType($this->problemTypeRepositoryMock->find($data->type));
        $quadraticEqTemplExpected->setDifficulty($this->difficultyRepositoryMock->find($data->difficulty));
        $quadraticEqTemplExpected->setSubCategory($this->subCategoryRepositoryMock->find($data->subCategory));
        $quadraticEqTemplExpected->setConditions(new ArrayCollection([
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
        $quadraticEqTemplExpected->setCreated($data->created);

        // Create QuadraticEqTempl
        $quadraticEqTempl = $this->functionality->create($data);

        // Test created QuadraticEqTempl against expected QuadraticEqTempl object
        $this->assertEquals($quadraticEqTemplExpected, $quadraticEqTempl);

        // Data for QuadraticEqTempl update
        $data = ArrayHash::from([
            'variable' => 'U',
            'body' => 'TEST_BODY_NEW',
            'textBefore' => 'TEST_textBefore_NEW',
            'textAfter' => 'TEST_TEXT_AFTER_NEW',
            'type' => 2,
            'difficulty' => 2,
            'subCategory' => 2,
            'matches' => '[]',
            'condition_1' => 1,
            'condition_2' => 1,
            'created' => new DateTime('2000-02-02')
        ]);

        // Prepare updated QuadraticEqTempl expected object
        $quadraticEqTemplExpected->setVariable($data->variable);
        $quadraticEqTemplExpected->setBody($data->body);
        $quadraticEqTemplExpected->setTextBefore($data->textBefore);
        $quadraticEqTemplExpected->setTextAfter($data->textAfter);
        $quadraticEqTemplExpected->setProblemType($this->problemTypeRepositoryMock->find($data->type));
        $quadraticEqTemplExpected->setDifficulty($this->difficultyRepositoryMock->find($data->difficulty));
        $quadraticEqTemplExpected->setSubCategory($this->subCategoryRepositoryMock->find($data->subCategory));
        $quadraticEqTemplExpected->setMatches($data->matches);
        $quadraticEqTemplExpected->setConditions(new ArrayCollection([
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
        $quadraticEqTemplExpected->setCreated($data->created);

        // Set expected return values for QuadraticEqTemplRepository find method
        $this->repositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use (
                $quadraticEqTempl
            ) {
                switch ($arg){
                    case 1:
                        return $quadraticEqTempl;
                }
                return false;
            });

        // Update QuadraticEqTempl
        $quadraticEqTempl = $this->functionality->update(1, $data);

        // Test updated QuadraticEqTempl against expected QuadraticEqTempl object
        $this->assertEquals($quadraticEqTemplExpected, $quadraticEqTempl);
    }
}
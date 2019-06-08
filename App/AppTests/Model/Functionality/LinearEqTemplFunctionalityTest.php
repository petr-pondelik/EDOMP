<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.6.19
 * Time: 11:28
 */

namespace App\AppTests\Model\Functionality;


use App\Model\Entity\LinearEqTempl;
use App\Model\Functionality\LinearEqTemplFunctionality;
use App\Model\Repository\LinearEqTemplRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;

/**
 * Class LinearEqTemplFunctionalityTest
 * @package App\AppTests\Model\Functionality
 */
class LinearEqTemplFunctionalityTest extends ProblemFunctionalityTestCase
{
    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        // Mock the LinearEqTemplRepository
        $this->repositoryMock = $this->getMockBuilder(LinearEqTemplRepository::class)
            ->setMethods(['find', 'getSequenceVal'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for LinearEqTemplRepository getSequenceVal method
        $this->repositoryMock->expects($this->any())
            ->method('getSequenceVal')
            ->willReturn(1);

        // Instantiate tested class
        $this->functionality = new LinearEqTemplFunctionality(
            $this->em, $this->repositoryMock, $this->problemTypeRepositoryMock, $this->problemConditionRepositoryMock,
            $this->difficultyRepositoryMock, $this->subCategoryRepositoryMock, $this->templateJsonDataRepositoryMock
        );
    }

    /**
     * @throws \Exception
     */
    public function testFunctionality(): void
    {
        // Data for LinearEqTempl create
        $data = ArrayHash::from([
            'variable' => 'T',
            'body' => 'TEST_BODY',
            'text_before' => 'TEST_TEXT_BEFORE',
            'text_after' => 'TEST_TEXT_AFTER',
            'type' => 1,
            'difficulty' => 1,
            'subcategory' => 1,
            'condition_1' => 0,
            'condition_2' => 0,
            'created' => new DateTime('2000-01-01')
        ]);

        // Prepare LinearEqTempl expected object
        $linearEqTemplExpected = new LinearEqTempl();
        $linearEqTemplExpected->setVariable($data->variable);
        $linearEqTemplExpected->setBody($data->body);
        $linearEqTemplExpected->setTextBefore($data->text_before);
        $linearEqTemplExpected->setTextAfter($data->text_after);
        $linearEqTemplExpected->setProblemType($this->problemTypeRepositoryMock->find($data->type));
        $linearEqTemplExpected->setDifficulty($this->difficultyRepositoryMock->find($data->difficulty));
        $linearEqTemplExpected->setSubCategory($this->subCategoryRepositoryMock->find($data->subcategory));
        $linearEqTemplExpected->setConditions(new ArrayCollection([
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
        $linearEqTemplExpected->setCreated($data->created);

        // Create LinearEqTempl
        $linearEqTempl = $this->functionality->create($data);

        // Test created LinearEqTempl against expected LinearEqTempl object
        $this->assertEquals($linearEqTemplExpected, $linearEqTempl);

        // Data for LinearEqTempl update
        $data = ArrayHash::from([
            'variable' => 'U',
            'body' => 'TEST_BODY_NEW',
            'text_before' => 'TEST_TEXT_BEFORE_NEW',
            'text_after' => 'TEST_TEXT_AFTER_NEW',
            'type' => 2,
            'difficulty' => 2,
            'subcategory' => 2,
            'matches' => '[]',
            'condition_1' => 1,
            'condition_2' => 1,
            'created' => new DateTime('2000-02-02')
        ]);

        // Prepare updated LinearEqTempl expected object
        $linearEqTemplExpected->setVariable($data->variable);
        $linearEqTemplExpected->setBody($data->body);
        $linearEqTemplExpected->setTextBefore($data->text_before);
        $linearEqTemplExpected->setTextAfter($data->text_after);
        $linearEqTemplExpected->setProblemType($this->problemTypeRepositoryMock->find($data->type));
        $linearEqTemplExpected->setDifficulty($this->difficultyRepositoryMock->find($data->difficulty));
        $linearEqTemplExpected->setSubCategory($this->subCategoryRepositoryMock->find($data->subcategory));
        $linearEqTemplExpected->setMatches($data->matches);
        $linearEqTemplExpected->setConditions(new ArrayCollection([
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
        $linearEqTemplExpected->setCreated($data->created);

        // Set expected return values for LinearEqTemplRepository find method
        $this->repositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use (
                $linearEqTempl
            ) {
                switch ($arg){
                    case 1:
                        return $linearEqTempl;
                }
                return false;
            });

        // Update LinearEqTempl
        $linearEqTempl = $this->functionality->update(1, $data);

        // Test updated LinearEqTempl against expected LinearEqTempl object
        $this->assertEquals($linearEqTemplExpected, $linearEqTempl);
    }
}
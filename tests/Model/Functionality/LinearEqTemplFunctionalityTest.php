<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.6.19
 * Time: 11:28
 */

namespace Tests\Model\Functionality;


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

        // Mock the LinearEquationTemplateRepository
        $this->repositoryMock = $this->getMockBuilder(LinearEqTemplRepository::class)
            ->setMethods(['find', 'getSequenceVal'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expected return values for LinearEquationTemplateRepository getSequenceVal method
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
        // Data for LinearEquationTemplate create
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

        // Prepare LinearEquationTemplate expected object
        $linearEqTemplExpected = new LinearEqTempl();
        $linearEqTemplExpected->setVariable($data->variable);
        $linearEqTemplExpected->setBody($data->body);
        $linearEqTemplExpected->setTextBefore($data->textBefore);
        $linearEqTemplExpected->setTextAfter($data->textAfter);
        $linearEqTemplExpected->setProblemType($this->problemTypeRepositoryMock->find($data->type));
        $linearEqTemplExpected->setDifficulty($this->difficultyRepositoryMock->find($data->difficulty));
        $linearEqTemplExpected->setSubCategory($this->subCategoryRepositoryMock->find($data->subCategory));
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

        // Create LinearEquationTemplate
        $linearEqTempl = $this->functionality->create($data);

        // Test created LinearEquationTemplate against expected LinearEquationTemplate object
        $this->assertEquals($linearEqTemplExpected, $linearEqTempl);

        // Data for LinearEquationTemplate update
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

        // Prepare updated LinearEquationTemplate expected object
        $linearEqTemplExpected->setVariable($data->variable);
        $linearEqTemplExpected->setBody($data->body);
        $linearEqTemplExpected->setTextBefore($data->textBefore);
        $linearEqTemplExpected->setTextAfter($data->textAfter);
        $linearEqTemplExpected->setProblemType($this->problemTypeRepositoryMock->find($data->type));
        $linearEqTemplExpected->setDifficulty($this->difficultyRepositoryMock->find($data->difficulty));
        $linearEqTemplExpected->setSubCategory($this->subCategoryRepositoryMock->find($data->subCategory));
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

        // Set expected return values for LinearEquationTemplateRepository find method
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

        // Update LinearEquationTemplate
        $linearEqTempl = $this->functionality->update(1, $data);

        // Test updated LinearEquationTemplate against expected LinearEquationTemplate object
        $this->assertEquals($linearEqTemplExpected, $linearEqTempl);
    }
}
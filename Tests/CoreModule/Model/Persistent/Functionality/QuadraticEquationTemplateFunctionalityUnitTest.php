<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.12.19
 * Time: 18:24
 */

namespace App\Tests\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\QuadraticEquationTemplate;
use App\CoreModule\Model\Persistent\Functionality\ProblemTemplate\QuadraticEquationTemplateFunctionality;
use App\CoreModule\Model\Persistent\Functionality\TemplateJsonDataFunctionality;
use App\Tests\MockTraits\Repository\QuadraticEquationTemplateRepositoryMockTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\DateTime;

/**
 * Class QuadraticEquationTemplateFunctionalityUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Functionality
 */
final class QuadraticEquationTemplateFunctionalityUnitTest extends ProblemFunctionalityUnitTestCase
{
    use QuadraticEquationTemplateRepositoryMockTrait;

    /**
     * @var QuadraticEquationTemplateFunctionality
     */
    protected $functionality;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpQuadraticEquationTemplateRepositoryMock();

        $templateJsonDataFunctionalityMock = $this->getMockBuilder(TemplateJsonDataFunctionality::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->functionality = new QuadraticEquationTemplateFunctionality(
            $this->em, $this->userRepositoryMock, $this->quadraticEquationTemplateRepositoryMock,
            $this->problemTypeRepositoryMock, $this->problemConditionTypeRepositoryMock,
            $this->problemConditionRepositoryMock, $this->difficultyRepositoryMock, $this->subThemeRepositoryMock,
            $this->templateJsonDataRepositoryMock, $templateJsonDataFunctionalityMock
        );
    }

    /**
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \Nette\Utils\JsonException
     */
    public function testCreate(): void
    {
        // Data for QuadraticEquationTemplate create
        $data = [
            'variable' => 'x',
            'body' => 'TEST_BODY',
            'textBefore' => 'TEST_TEXT_BEFORE',
            'textAfter' => 'TEST_TEXT_AFTER',
            'type' => 1,
            'difficulty' => 1,
            'subTheme' => 1,
            'condition_1' => 0,
            'condition_2' => 0,
            'userId' => 1,
            'created' => DateTime::from($this->dateTimeStr)
        ];

        // Prepare LinearEquationTemplate expected object
        $expected = new QuadraticEquationTemplate();
        $expected->setVariable($data['variable']);
        $expected->setBody($data['body']);
        $expected->setTextBefore($data['textBefore']);
        $expected->setTextAfter($data['textAfter']);
        $expected->setProblemType($this->problemTypeRepositoryMock->find($data['type']));
        $expected->setDifficulty($this->difficultyRepositoryMock->find($data['difficulty']));
        $expected->setSubTheme($this->subThemeRepositoryMock->find($data['subTheme']));
        $expected->setConditions(new ArrayCollection([
                $this->problemConditionRepositoryMock->findOneBy([
                    'problemConditionType.id' => 1,
                    'accessor' => $data['condition_1']
                ]),
                $this->problemConditionRepositoryMock->findOneBy([
                    'problemConditionType.id' => 2,
                    'accessor' => $data['condition_2']
                ])
            ])
        );
        $expected->setCreatedBy($this->userRepositoryMock->find($data['userId']));
        $expected->setCreated($data['created']);

        // Create QuadraticEquationTemplate
        $created = $this->functionality->create($data);

        $this->assertEquals($expected, $created);
    }

    /**
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     * @throws EntityNotFoundException
     */
    public function testCreateDifficultyFail(): void
    {
        // Data for create
        $data = [
            'variable' => 'x',
            'body' => 'TEST_BODY',
            'textBefore' => 'TEST_TEXT_BEFORE',
            'textAfter' => 'TEST_TEXT_AFTER',
            'type' => 1,
            'difficulty' => 100,
            'subTheme' => 1,
            'condition_1' => 0,
            'condition_2' => 0,
            'userId' => 1,
            'created' => DateTime::from($this->dateTimeStr)
        ];

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Difficulty not found.');
        $this->functionality->create($data);
    }

    /**
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     * @throws EntityNotFoundException
     */
    public function testCreateSubThemeFail(): void
    {
        // Data for ProblemFinal create
        $data = [
            'variable' => 'x',
            'body' => 'TEST_BODY',
            'textBefore' => 'TEST_TEXT_BEFORE',
            'textAfter' => 'TEST_TEXT_AFTER',
            'type' => 1,
            'difficulty' => 1,
            'subTheme' => 100,
            'condition_1' => 0,
            'condition_2' => 0,
            'userId' => 1,
            'created' => DateTime::from($this->dateTimeStr)
        ];

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('SubTheme not found.');
        $this->functionality->create($data);
    }

    /**
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     * @throws EntityNotFoundException
     */
    public function testCreateProblemTypeFail(): void
    {
        // Data for create
        $data = [
            'variable' => 'x',
            'body' => 'TEST_BODY',
            'textBefore' => 'TEST_TEXT_BEFORE',
            'textAfter' => 'TEST_TEXT_AFTER',
            'type' => 100,
            'difficulty' => 1,
            'subTheme' => 1,
            'condition_1' => 0,
            'condition_2' => 0,
            'userId' => 1,
            'created' => DateTime::from($this->dateTimeStr)
        ];

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('ProblemType not found.');
        $this->functionality->create($data);
    }

    /**
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     * @throws EntityNotFoundException
     */
    public function testCreateUserFail(): void
    {
        // Data for create
        $data = [
            'variable' => 'x',
            'body' => 'TEST_BODY',
            'textBefore' => 'TEST_TEXT_BEFORE',
            'textAfter' => 'TEST_TEXT_AFTER',
            'type' => 1,
            'difficulty' => 1,
            'subTheme' => 1,
            'condition_1' => 0,
            'condition_2' => 0,
            'userId' => 100,
            'created' => DateTime::from($this->dateTimeStr)
        ];

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('User not found.');
        $this->functionality->create($data);
    }

    public function testUpdate(): void
    {
        // Data for LinearEquationTemplate update
        $data = [
            'variable' => 'U',
            'body' => 'TEST_BODY_NEW',
            'textBefore' => 'TEST_textBefore_NEW',
            'textAfter' => 'TEST_TEXT_AFTER_NEW',
            'type' => 2,
            'difficulty' => 2,
            'subTheme' => 2,
            'matches' => '[]',
            'condition_1' => 1,
            'condition_2' => 1,
            'userId' => 2,
            'created' => DateTime::from($this->dateTimeStr)
        ];

        // Prepare updated QuadraticEquationTemplate expected object
        /** @var QuadraticEquationTemplate $expected */
        $expected = $this->quadraticEquationTemplateRepositoryMock->find(1);
        $expected->setVariable($data['variable']);
        $expected->setBody($data['body']);
        $expected->setTextBefore($data['textBefore']);
        $expected->setTextAfter($data['textAfter']);
        $expected->setProblemType($this->problemTypeRepositoryMock->find($data['type']));
        $expected->setDifficulty($this->difficultyRepositoryMock->find($data['difficulty']));
        $expected->setSubTheme($this->subThemeRepositoryMock->find($data['subTheme']));
        $expected->setMatches($data['matches']);
        $expected->setConditions(new ArrayCollection([
                $this->problemConditionRepositoryMock->findOneBy([
                    'problemConditionType.id' => 1,
                    'accessor' => $data['condition_1']
                ]),
                $this->problemConditionRepositoryMock->findOneBy([
                    'problemConditionType.id' => 2,
                    'accessor' => $data['condition_2']
                ])
            ])
        );
        $expected->setCreatedBy($this->userRepositoryMock->find($data['userId']));
        $expected->setCreated($data['created']);

        // Update QuadraticEquationTemplate
        $updated = $this->functionality->update(1, $data);

        // Test updated QuadraticEquationTemplate against expected QuadraticEquationTemplate object
        $this->assertEquals($expected, $updated);
    }
}
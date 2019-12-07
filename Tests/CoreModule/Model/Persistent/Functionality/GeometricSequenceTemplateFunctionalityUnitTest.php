<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.12.19
 * Time: 11:52
 */

namespace App\Tests\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\GeometricSequenceTemplate;
use App\CoreModule\Model\Persistent\Functionality\ProblemTemplate\GeometricSequenceTemplateFunctionality;
use App\CoreModule\Model\Persistent\Functionality\TemplateJsonDataFunctionality;
use App\Tests\MockTraits\Repository\GeometricSequenceTemplateRepositoryMockTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\DateTime;

/**
 * Class GeometricSequenceTemplateFunctionalityUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Functionality
 */
final class GeometricSequenceTemplateFunctionalityUnitTest extends ProblemFunctionalityUnitTestCase
{
    use GeometricSequenceTemplateRepositoryMockTrait;

    /**
     * @var GeometricSequenceTemplateFunctionality
     */
    protected $functionality;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpGeometricSequenceTemplateRepositoryMock();

        $templateJsonDataFunctionalityMock = $this->getMockBuilder(TemplateJsonDataFunctionality::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->functionality = new GeometricSequenceTemplateFunctionality(
            $this->em, $this->userRepositoryMock, $this->geometricSequenceTemplateRepositoryMock,
            $this->problemTypeRepositoryMock, $this->problemConditionTypeRepositoryMock,
            $this->problemConditionRepositoryMock, $this->difficultyRepositoryMock, $this->subThemeRepositoryMock,
            $this->templateJsonDataRepositoryMock, $templateJsonDataFunctionalityMock
        );
    }

    public function testCreate(): void
    {
        // Data for GeometricSequenceTemplate create
        $data = [
            'indexVariable' => 'T',
            'body' => 'TEST_BODY',
            'textBefore' => 'TEST_TEXT_BEFORE',
            'textAfter' => 'TEST_TEXT_AFTER',
            'firstN' => 5,
            'type' => 1,
            'difficulty' => 1,
            'subTheme' => 1,
            'condition_1' => 0,
            'condition_2' => 0,
            'userId' => 1,
            'created' => DateTime::from($this->dateTimeStr)
        ];

        // Prepare GeometricSequenceTemplate expected object
        $expected = new GeometricSequenceTemplate();
        $expected->setIndexVariable($data['indexVariable']);
        $expected->setBody($data['body']);
        $expected->setTextBefore($data['textBefore']);
        $expected->setTextAfter($data['textAfter']);
        $expected->setFirstN($data['firstN']);
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

        // Create GeometricSequenceTemplate
        $created = $this->functionality->create($data);

        // Test created GeometricSequenceTemplate against expected GeometricSequenceTemplate object
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
            'indexVariable' => 'n',
            'firstN' => 5,
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
            'indexVariable' => 'n',
            'firstN' => 5,
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
            'indexVariable' => 'n',
            'firstN' => 5,
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
            'indexVariable' => 'n',
            'firstN' => 5,
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

    /**
     * @throws EntityNotFoundException
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     */
    public function testUpdate(): void
    {
        // Data for GeometricSequenceTemplate update
        $data = [
            'indexVariable' => 'u',
            'firstN' => 6,
            'body' => 'TEST_BODY_NEW',
            'textBefore' => 'TEST_TEST_BEFORE_NEW',
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

        // Prepare updated GeometricSequenceTemplate expected object
        /** @var GeometricSequenceTemplate $expected */
        $expected = $this->geometricSequenceTemplateRepositoryMock->find(1);
        $expected->setIndexVariable($data['indexVariable']);
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

        // Update GeometricSequenceTemplate
        $updated = $this->functionality->update(1, $data);

        // Test updated GeometricSequenceTemplate against expected GeometricSequenceTemplate object
        $this->assertEquals($expected, $updated);
    }
}
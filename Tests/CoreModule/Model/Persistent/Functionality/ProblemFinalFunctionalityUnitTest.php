<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.12.19
 * Time: 13:43
 */

namespace App\Tests\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Helpers\FormatterHelper;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal;
use App\CoreModule\Model\Persistent\Functionality\ProblemFinalFunctionality;
use App\Tests\MockTraits\Repository\ProblemFinalRepositoryMockTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ProblemFinalFunctionalityUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Functionality
 */
final class ProblemFinalFunctionalityUnitTest extends ProblemFunctionalityUnitTestCase
{
    use ProblemFinalRepositoryMockTrait;

    /**
     * @var MockObject
     */
    protected $formatterHelperMock;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpProblemFinalRepositoryMock();

        // Mock the FormatterHelper
        $this->formatterHelperMock = $this->getMockBuilder(FormatterHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Create functionality
        $this->functionality = new ProblemFinalFunctionality(
            $this->em, $this->problemFinalRepositoryMock, $this->problemTemplateRepositoryMock, $this->userRepositoryMock,
            $this->problemTypeRepositoryMock, $this->problemConditionRepositoryMock, $this->difficultyRepositoryMock,
            $this->subThemeRepositoryMock, $this->formatterHelperMock
        );
    }

    public function testCreate(): void
    {
        // Data for ProblemFinal create
        $data = [
            'body' => 'TEST_BODY',
            'textBefore' => 'TEST_TEXT_BEFORE',
            'textAfter' => 'TEST_TEXT_AFTER',
            'problemType' => 1,
            'difficulty' => 1,
            'subTheme' => 1,
            'condition_1' => 0,
            'condition_2' => 0,
            'userId' => 1,
            'created' => DateTime::from($this->dateTimeStr)
        ];

        // Prepare ProblemFinal expected object
        $expected = new ProblemFinal();
        $expected->setBody($data['body']);
        $expected->setTextBefore($data['textBefore']);
        $expected->setTextAfter($data['textAfter']);
        $expected->setProblemType($this->problemTypeRepositoryMock->find($data['problemType']));
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

        // Create ProblemFinal
        $created = $this->functionality->create($data);

        // Test created ProblemFinal against expected ProblemFinal object
        $this->assertEquals($expected, $created);

        // Data for ProblemFinal create
        $data = [
            'body' => 'TEST_BODY_SECOND',
            'textBefore' => 'TEST_TEXT_BEFORE_SECOND',
            'textAfter' => 'TEST_TEXT_AFTER_SECOND',
            'problemType' => 2,
            'difficulty' => 2,
            'subTheme' => 2,
            'problemTemplateId' => 1,
            'condition_1' => 1,
            'condition_2' => 0,
            'userId' => 2,
            'created' => DateTime::from($this->dateTimeStr),
            'isGenerated' => true,
            'studentVisible' => false
        ];

        // Prepare ProblemFinal expected object
        $expected = new ProblemFinal();
        $expected->setBody($data['body']);
        $expected->setTextBefore($data['textBefore']);
        $expected->setTextAfter($data['textAfter']);
        $expected->setProblemType($this->problemTypeRepositoryMock->find($data['problemType']));
        $expected->setDifficulty($this->difficultyRepositoryMock->find($data['difficulty']));
        $expected->setSubTheme($this->subThemeRepositoryMock->find($data['subTheme']));
        $expected->setProblemTemplate($this->problemTemplateRepositoryMock->find($data['problemTemplateId']));
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
        $expected->setIsGenerated($data['isGenerated']);
        $expected->setStudentVisible($data['studentVisible']);

        // Create ProblemFinal
        $created = $this->functionality->create($data);

        // Test created ProblemFinal against expected ProblemFinal object
        $this->assertEquals($expected, $created);
    }

    public function testCreateDifficultyFail(): void
    {
        // Data for ProblemFinal create
        $data = [
            'body' => 'TEST_BODY',
            'textBefore' => 'TEST_TEXT_BEFORE',
            'textAfter' => 'TEST_TEXT_AFTER',
            'problemType' => 1,
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

    public function testCreateSubThemeFail(): void
    {
        // Data for ProblemFinal create
        $data = [
            'body' => 'TEST_BODY',
            'textBefore' => 'TEST_TEXT_BEFORE',
            'textAfter' => 'TEST_TEXT_AFTER',
            'problemType' => 1,
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

    public function testCreateProblemTypeFail(): void
    {
        // Data for ProblemFinal create
        $data = [
            'body' => 'TEST_BODY',
            'textBefore' => 'TEST_TEXT_BEFORE',
            'textAfter' => 'TEST_TEXT_AFTER',
            'problemType' => 100,
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

    public function testCreateProblemTemplateFail(): void
    {
        // Data for ProblemFinal create
        $data = [
            'body' => 'TEST_BODY',
            'textBefore' => 'TEST_TEXT_BEFORE',
            'textAfter' => 'TEST_TEXT_AFTER',
            'problemType' => 1,
            'difficulty' => 1,
            'subTheme' => 1,
            'condition_1' => 0,
            'condition_2' => 0,
            'problemTemplateId' => 100,
            'userId' => 1,
            'created' => DateTime::from($this->dateTimeStr)
        ];

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('ProblemTemplate not found.');
        $this->functionality->create($data);
    }

    public function testCreateUserFail(): void
    {
        // Data for ProblemFinal create
        $data = [
            'body' => 'TEST_BODY',
            'textBefore' => 'TEST_TEXT_BEFORE',
            'textAfter' => 'TEST_TEXT_AFTER',
            'problemType' => 1,
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
        // Data for ProblemFinal update
        $data = [
            'body' => '15x + 10x - 5 = 0',
            'textBefore' => 'TEST_TEST_BEFORE_NEW',
            'textAfter' => 'TEST_TEXT_AFTER_NEW',
            'problemType' => 2,
            'difficulty' => 2,
            'subTheme' => 2,
            'condition_1' => 1,
            'condition_2' => 1,
            'created' => DateTime::from($this->dateTimeStr),
            'studentVisible' => false
        ];

        // Prepare updated ProblemFinal expected object
        $expected = $this->firstProblemFinal;
        $expected->setBody($data['body']);
        $expected->setTextBefore($data['textBefore']);
        $expected->setTextAfter($data['textAfter']);
        $expected->setProblemType($this->problemTypeRepositoryMock->find($data['problemType']));
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
        $expected->setCreated($data['created']);
        $expected->setStudentVisible($data['studentVisible']);

        // Update ProblemFinal
        $updated = $this->functionality->update(1, $data);

        // Test updated ProblemFinal against expected ProblemFinal object
        $this->assertEquals($expected, $updated);

//        // Store result for the ProblemFinal entity
//        $this->functionality->storeResult(1, ArrayHash::from([
//            'x' => 0.2
//        ]));
//
//        // Test expected ProblemFinal's result format
//        $this->assertEquals('$$ x = 0.2 $$', $problemFinal->getResult());
    }
}
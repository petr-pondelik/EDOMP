<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.11.19
 * Time: 16:42
 */

namespace App\Tests\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Entity\ProblemFinal;
use App\Tests\Traits\DifficultySetUpTrait;
use App\Tests\Traits\SubThemeMockSetUpTrait;
use App\Tests\Traits\UserMockSetUpTrait;

/**
 * Class ProblemFinalUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
final class ProblemFinalUnitTest extends PersistentEntityTestCase
{
    use DifficultySetUpTrait;
    use SubThemeMockSetUpTrait;
    use UserMockSetUpTrait;

    /**
     * @var array
     */
    protected $errorMessages = [
        "Body can't be blank.",
        "Difficulty can't be blank.",
        "SubTheme can't be blank."
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDifficultyMock();
        $this->setUpSubThemeMock();
        $this->setUpUserMock();
    }

    public function testValidState(): void
    {
        $entity = new ProblemFinal();
        $body = '$$ x + 3 = 4 $$';
        $textBefore = 'TEST_TEXT_BEFORE';
        $textAfter = 'TEST_TEXT_AFTER';
        $matchesIndex = 10;
        $result = 'TEST_RESULT';

        $entity->setBody($body);
        $entity->setDifficulty($this->difficultyMock);
        $entity->setSubTheme($this->subThemeMock);

        $this->assertInstanceOf(ProblemFinal::class, $entity);
        $this->assertTrue($entity->isTeacherLevelSecured());
        $this->assertEquals($body, $entity->getBody());
        $this->assertEquals($this->difficultyMock, $entity->getDifficulty());
        $this->assertEquals($this->subThemeMock, $entity->getSubTheme());
        $this->assertNull($entity->getCreatedBy());
        $this->assertNull($entity->getTextBefore());
        $this->assertNull($entity->getTextAfter());
        $this->assertNull($entity->getMatchesIndex());
        $this->assertNull($entity->getResult());

        $entity->setCreatedBy($this->userMock);
        $entity->setTextBefore($textBefore);
        $entity->setTextAfter($textAfter);
        $entity->setMatchesIndex($matchesIndex);
        $entity->setResult($result);

        $this->assertEquals($this->userMock, $entity->getCreatedBy());
        $this->assertEquals($textBefore, $entity->getTextBefore());
        $this->assertEquals($textAfter, $entity->getTextAfter());
        $this->assertEquals($matchesIndex, $entity->getMatchesIndex());
        $this->assertEquals($result, $entity->getResult());

        $this->assertValidByValidator($entity);
    }

    public function testInvalidState(): void
    {
        $entity = new ProblemFinal();
        $this->assertValidatorViolations($entity);
    }
}
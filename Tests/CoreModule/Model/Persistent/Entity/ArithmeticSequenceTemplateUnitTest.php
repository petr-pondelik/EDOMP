<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.11.19
 * Time: 17:23
 */

namespace App\Tests\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ArithmeticSequenceTemplate;
use App\Tests\MockTraits\Entity\DifficultySetUpTrait;
use App\Tests\MockTraits\Entity\SubThemeMockSetUpTrait;
use App\Tests\MockTraits\Entity\UserMockSetUpTrait;

/**
 * Class ArithmeticSequenceTemplateUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
final class ArithmeticSequenceTemplateUnitTest extends PersistentEntityTestCase
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
        "SubTheme can't be blank.",
        "IndexVariable can't be blank.",
        "FirstN can't be blank."
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
        $entity = new ArithmeticSequenceTemplate();
        $body = '$$ x + 3 = 4 $$';
        $textBefore = 'TEST_TEXT_BEFORE';
        $textAfter = 'TEST_TEXT_AFTER';
        $indexVariable = 'n';
        $firstN = 5;
        $matches = '[{\"p0\":-5},{\"p0\":-4},{\"p0\":-3},{\"p0\":-2},{\"p0\":-1},{\"p0\":0},{\"p0\":1},{\"p0\":2},{\"p0\":3},{\"p0\":4},{\"p0\":5}]';

        $entity->setBody($body);
        $entity->setDifficulty($this->difficultyMock);
        $entity->setSubTheme($this->subThemeMock);
        $entity->setIndexVariable($indexVariable);
        $entity->setFirstN($firstN);

        $this->assertInstanceOf(ArithmeticSequenceTemplate::class, $entity);
        $this->assertTrue($entity->isTeacherLevelSecured());
        $this->assertEquals($body, $entity->getBody());
        $this->assertEquals($this->difficultyMock, $entity->getDifficulty());
        $this->assertEquals($this->subThemeMock, $entity->getSubTheme());
        $this->assertEquals($indexVariable, $entity->getIndexVariable());
        $this->assertEquals($firstN, $entity->getFirstN());
        $this->assertNull($entity->getCreatedBy());
        $this->assertNull($entity->getTextBefore());
        $this->assertNull($entity->getTextAfter());
        $this->assertNull($entity->getMatches());

        $entity->setCreatedBy($this->userMock);
        $entity->setTextBefore($textBefore);
        $entity->setTextAfter($textAfter);
        $entity->setMatches($matches);

        $this->assertEquals($this->userMock, $entity->getCreatedBy());
        $this->assertEquals($textBefore, $entity->getTextBefore());
        $this->assertEquals($textAfter, $entity->getTextAfter());
        $this->assertEquals($matches, $entity->getMatches());

        $this->assertValidByValidator($entity);
    }

    public function testInvalidState(): void
    {
        $entity = new ArithmeticSequenceTemplate();
        $this->assertValidatorViolations($entity);
    }
}
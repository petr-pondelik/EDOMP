<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.11.19
 * Time: 17:32
 */

namespace App\Tests\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Entity\Test;
use App\CoreModule\Model\Persistent\Entity\TestVariant;
use App\Tests\MockTraits\Entity\GroupMockSetUpTrait;
use App\Tests\MockTraits\Entity\LogoMockSetUpTrait;
use App\Tests\MockTraits\Entity\TestVariantMockSetUpTrait;
use App\Tests\MockTraits\Entity\UserMockSetUpTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class TestUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
final class TestUnitTest extends PersistentEntityTestCase
{
    use UserMockSetUpTrait;
    use GroupMockSetUpTrait;
    use TestVariantMockSetUpTrait;
    use LogoMockSetUpTrait;

    /**
     * @var array
     */
    protected $errorMessages = [
        0 => "SchoolYear can't be blank.",
        1 => "TestNumber can't be blank.",
        2 => "VariantsCnt can't be blank.",
        3 => "ProblemsPerVariant can't be blank.",
        4 => "Term can't be blank.",
        5 => "Logo can't be blank.",
        6 => 'Test must be target at least for one group.',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpUserMock();
        $this->setUpGroupMock();
        $this->setUpTestVariantMock();
        $this->setUpLogoMock();
    }

    public function testValidState(): void
    {
        $entity = new Test();
        $schoolYear = '2018/19';
        $testNumber = 1;
        $variantsCnt = 3;
        $problemsPerVariant = 5;
        $term = '1. pol.';
        $groups = new ArrayCollection([$this->groupMock]);
        $testVariants = new ArrayCollection([$this->testVariantMock]);

        $this->assertInstanceOf(Test::class, $entity);
        $this->assertTrue($entity->isTeacherLevelSecured());
        $this->assertFalse($entity->isClosed());

        $entity->setId(1);
        $entity->setSchoolYear($schoolYear);
        $entity->setTestNumber($testNumber);
        $entity->setVariantsCnt($variantsCnt);
        $entity->setProblemsPerVariant($problemsPerVariant);
        $entity->setTerm($term);
        $entity->setLogo($this->logoMock);
        $entity->setGroups($groups);
        $entity->setTestVariants($testVariants);

        $this->assertEquals('1', (string) $entity);
        $this->assertEquals($schoolYear, $entity->getSchoolYear());
        $this->assertEquals($testNumber, $entity->getTestNumber());
        $this->assertEquals($variantsCnt, $entity->getVariantsCnt());
        $this->assertEquals($problemsPerVariant, $entity->getProblemsPerVariant());
        $this->assertEquals($term, $entity->getTerm());
        $this->assertEquals($groups, $entity->getGroups());
        $this->assertEquals($testVariants, $entity->getTestVariants());

        $entity->addGroup($this->groupMock);
        $entity->addTestVariant($this->testVariantMock);

        $this->assertEquals($groups, $entity->getGroups());
        $this->assertEquals($testVariants, $entity->getTestVariants());

        /**
         * @var TestVariant $secondVariantMock
         */
        $secondVariantMock = $this->getMockBuilder(TestVariant::class)->disableOriginalConstructor()->getMock();
        $secondVariantMock->setId(2);
        $secondVariantMock->setLabel('SECOND_VARIANT_MOCK');
        $entity->addTestVariant($secondVariantMock);
        $testVariantsSecond = new ArrayCollection([$this->testVariantMock, $secondVariantMock]);
        $this->assertCount(2, $testVariantsSecond->getValues());
        $this->assertEquals($testVariantsSecond, $entity->getTestVariants());

        $this->assertValidByValidator($entity);
    }

    public function testInvalidState(): void
    {
        $entity = new Test();
        $this->assertValidatorViolations($entity);

        $this->errorMessages[0] = 'SchoolYear is not valid.';
        $this->errorMessages[1] = 'TestNumber must be greater or equal to 0.';

        $entity->setSchoolYear('asdf');
        $entity->setTestNumber(-1);

        $this->assertValidatorViolations($entity);
    }
}
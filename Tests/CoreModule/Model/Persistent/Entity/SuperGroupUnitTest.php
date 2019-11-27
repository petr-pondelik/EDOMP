<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.11.19
 * Time: 19:48
 */

declare(strict_types = 1);

namespace App\Tests\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Entity\SuperGroup;
use App\CoreModule\Model\Persistent\Entity\Theme;
use App\Tests\Traits\GroupMockSetUpTrait;
use App\Tests\Traits\ThemeMockSetUpTrait;
use App\Tests\Traits\UserMockSetUpTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class SuperGroupUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
class SuperGroupUnitTest extends PersistentEntityTestCase
{
    use GroupMockSetUpTrait;
    use ThemeMockSetUpTrait;
    use UserMockSetUpTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpThemeMock();
        $this->setUpGroupMock();
        $this->setUpUserMock();
    }

    /**
     * @var array
     */
    protected $errorMessages = [
        0 => "Label can't be blank."
    ];

    public function testValidState(): void
    {
        $entity = new SuperGroup();
        $label = 'TEST_LABEL';
        $groups = new ArrayCollection([$this->groupMock]);
        $themes = new ArrayCollection([$this->themeMock]);

        $entity->setLabel($label);

        $this->assertEquals(true, $entity->isTeacherLevelSecured());
        $this->assertEquals($label, $entity->getLabel());
        $this->assertEquals(new ArrayCollection([]), $entity->getGroups());
        $this->assertEquals(new ArrayCollection([]), $entity->getThemes());

        $entity->setGroups($groups);
        $entity->setThemes($themes);

        $this->assertEquals($groups, $entity->getGroups());
        $this->assertEquals($themes, $entity->getThemes());

        $entity->addTheme($this->themeMock);
        $this->assertCount(1, $entity->getThemes());
        $this->assertEquals($entity->getThemes(), $themes);

        /**
         * @var Theme $themeMockSecond
         */
        $themeMockSecond = $this->getMockBuilder(Theme::class)->disableOriginalConstructor()->getMock();
        $themeMockSecond->setLabel('TEST_LABEL_SECOND');
        $themeMockSecond->setId(2);

        $entity->addTheme($themeMockSecond);
        $this->assertCount(2, $entity->getThemes()->getValues());
        $this->assertEquals($entity->getThemes(), new ArrayCollection([$this->themeMock, $themeMockSecond]));

        $this->assertValidByValidator($entity);
    }

    public function testInvalidState(): void
    {
        $entity = new SuperGroup();
        $this->assertValidatorViolations($entity);
    }
}
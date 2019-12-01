<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.11.19
 * Time: 23:24
 */

declare(strict_types = 1);

namespace App\Tests\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Entity\Group;
use App\CoreModule\Model\Persistent\Entity\Theme;
use App\Tests\MockTraits\Entity\SuperGroupMockSetUpTrait;
use App\Tests\MockTraits\Entity\ThemeMockSetUpTrait;
use App\Tests\MockTraits\Entity\UserMockSetUpTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class GroupUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
final class GroupUnitTest extends PersistentEntityTestCase
{
    use UserMockSetUpTrait;
    use SuperGroupMockSetUpTrait;
    use ThemeMockSetUpTrait;

    /**
     * @var array
     */
    protected $errorMessages = [
        0 => "SuperGroup can't be blank.",
        1 => "Label can't be blank.",
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpUserMock();
        $this->setUpSuperGroupMock();
        $this->setUpThemeMock();
    }

    public function testValidState(): void
    {
        $entity = new Group();
        $label = 'TEST_LABEL';
        $users = new ArrayCollection([$this->userMock]);
        $themes = new ArrayCollection([$this->themeMock]);

        $entity->setLabel($label);
        $entity->setSuperGroup($this->superGroupMock);

        $this->assertEquals(true, $entity->isTeacherLevelSecured());
        $this->assertEquals($label, $entity->getLabel());
        $this->assertEquals(new ArrayCollection([]), $entity->getThemes());
        $this->assertEquals(new ArrayCollection([]), $entity->getUsers());

        $entity->setThemes($themes);
        $entity->setUsers($users);

        $this->assertEquals($users, $entity->getUsers());
        $this->assertEquals($themes, $entity->getThemes());

        $entity->addTheme($this->themeMock);
        $this->assertCount(1, $entity->getThemes());
        $this->assertEquals($themes, $entity->getThemes());

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
        $entity = new Group();
        $this->assertValidatorViolations($entity);
    }
}
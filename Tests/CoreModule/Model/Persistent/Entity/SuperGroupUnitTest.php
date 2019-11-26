<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.11.19
 * Time: 19:48
 */

namespace App\Tests\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Entity\SuperGroup;
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
    }

    public function testInvalidState(): void
    {
        $entity = new SuperGroup();
        $this->assertValidatorViolations($entity);
    }
}
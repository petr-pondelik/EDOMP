<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.11.19
 * Time: 22:32
 */

declare(strict_types = 1);

namespace App\Tests\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Entity\Theme;
use App\Tests\MockTraits\Entity\GroupMockSetUpTrait;
use App\Tests\MockTraits\Entity\SubThemeMockSetUpTrait;
use App\Tests\MockTraits\Entity\SuperGroupMockSetUpTrait;
use App\Tests\MockTraits\Entity\UserMockSetUpTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class CategoryTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
final class ThemeUnitTest extends PersistentEntityTestCase
{
    use GroupMockSetUpTrait;
    use UserMockSetUpTrait;
    use SubThemeMockSetUpTrait;
    use SuperGroupMockSetUpTrait;

    /**
     * @var array
     */
    protected $errorMessages = [
        0 => "Label can't be blank."
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpGroupMock();
        $this->setUpUserMock();
        $this->setUpSubThemeMock();
        $this->setUpSuperGroupMock();
    }

    public function testValidState(): void
    {
        $entity = new Theme();
        $entity->setLabel('TEST_LABEL');
        $entity->setCreatedBy($this->userMock);

        $this->assertEquals(true, $entity->isTeacherLevelSecured());
        $this->assertEquals('TEST_LABEL', $entity->getLabel());
        $this->assertEquals($this->userMock, $entity->getCreatedBy());
        $this->assertEquals(new ArrayCollection(), $entity->getGroups());
        $this->assertEquals(new ArrayCollection(), $entity->getSubThemes());
        $this->assertEquals(new ArrayCollection(), $entity->getSuperGroups());

        $entity->setGroups(new ArrayCollection([$this->groupMock]));
        $entity->setSubThemes(new ArrayCollection([$this->subThemeMock]));
        $entity->setSuperGroups(new ArrayCollection([$this->superGroupMock]));

        $this->assertEquals(new ArrayCollection([$this->groupMock]), $entity->getGroups());
        $this->assertEquals(new ArrayCollection([$this->subThemeMock]), $entity->getSubThemes());
        $this->assertEquals(new ArrayCollection([$this->superGroupMock]), $entity->getSuperGroups());

        $this->assertValidByValidator($entity);
    }

    public function testInvalidState(): void
    {
        $entity = new Theme();
        $this->assertValidatorViolations($entity);
    }
}
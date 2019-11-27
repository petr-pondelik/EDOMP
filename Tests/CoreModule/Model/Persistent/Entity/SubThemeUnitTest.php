<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.11.19
 * Time: 1:04
 */

declare(strict_types = 1);

namespace App\Tests\CoreModule\Model\Persistent\Entity;


use App\CoreModule\Model\Persistent\Entity\SubTheme;
use App\Tests\Traits\ProblemFinalMockSetUpTrait;
use App\Tests\Traits\ThemeMockSetUpTrait;
use App\Tests\Traits\UserMockSetUpTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class SubThemeUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
class SubThemeUnitTest extends PersistentEntityTestCase
{
    use ProblemFinalMockSetUpTrait;
    use ThemeMockSetUpTrait;
    use UserMockSetUpTrait;

    /**
     * @var array
     */
    protected $errorMessages = [
        0 => "Theme can't be blank.",
        1 => "Label can't be blank."
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpProblemFinalMock();
        $this->setUpThemeMock();
        $this->setUpUserMock();
    }

    public function testValidState(): void
    {
        $entity = new SubTheme();
        $label = 'TEST_LABEL';

        $entity->setLabel($label);

        $this->assertEquals(true, $entity->isTeacherLevelSecured());
        $this->assertEquals($label, $entity->getLabel());
        $this->assertEquals(new ArrayCollection(), $entity->getProblems());

        $entity->setCreatedBy($this->userMock);
        $entity->setProblems(new ArrayCollection([$this->problemFinalMock]));
        $entity->setTheme($this->themeMock);

        $this->assertEquals($this->userMock, $entity->getCreatedBy());
        $this->assertEquals(new ArrayCollection([$this->problemFinalMock]), $entity->getProblems());
        $this->assertEquals($this->themeMock, $entity->getTheme());

        $this->assertValidByValidator($entity);
    }

    public function testInvalidState(): void
    {
        $entity = new SubTheme();
        $this->assertValidatorViolations($entity);
    }
}
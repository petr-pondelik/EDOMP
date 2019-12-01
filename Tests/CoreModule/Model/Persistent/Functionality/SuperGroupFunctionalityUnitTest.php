<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.12.19
 * Time: 15:07
 */

namespace App\Tests\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\SuperGroup;
use App\CoreModule\Model\Persistent\Functionality\GroupFunctionality;
use App\CoreModule\Model\Persistent\Functionality\SuperGroupFunctionality;
use App\Tests\MockTraits\Repository\GroupRepositoryMockTrait;
use App\Tests\MockTraits\Repository\SuperGroupRepositoryMockTrait;
use App\Tests\MockTraits\Repository\ThemeRepositoryMockTrait;
use App\Tests\MockTraits\Repository\UserRepositoryMockTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;

/**
 * Class SuperGroupFunctionalityUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Functionality
 */
final class SuperGroupFunctionalityUnitTest extends FunctionalityUnitTestCase
{
    use SuperGroupRepositoryMockTrait;
    use ThemeRepositoryMockTrait;
    use GroupRepositoryMockTrait;
    use UserRepositoryMockTrait;

    /**
     * @var SuperGroupFunctionality
     */
    protected $functionality;

    /**
     * @var GroupFunctionality
     */
    protected $groupFunctionality;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpSuperGroupRepositoryMock();
        $this->setUpThemeRepositoryMock();
        $this->setUpGroupRepositoryMock();
        $this->setUpUserRepositoryMock();
        $this->groupFunctionality = new GroupFunctionality($this->em, $this->groupRepositoryMock, $this->superGroupRepositoryMock, $this->themeRepositoryMock, $this->userRepositoryMock);
        $this->functionality = new SuperGroupFunctionality($this->em, $this->superGroupRepositoryMock, $this->themeRepositoryMock, $this->userRepositoryMock, $this->groupFunctionality);
    }

    public function testCreate(): void
    {
        // Data for SuperGroup create
        $data = ArrayHash::from([
            'label' => 'TEST_SUPER_GROUP_FIRST',
            'created' => DateTime::from($this->dateTimeStr),
            'userId' => 1
        ]);

        // Prepare expected SuperGroup
        $expected = $this->firstSuperGroup;
        $expected->setCreatedBy($this->userRepositoryMock->find(1));

        // Create SuperGroup and test expected data
        $created = $this->functionality->create($data);
        $created->setId(1);
        $this->assertEquals($expected, $created);
    }

    public function testCreateUserNotFound(): void
    {
        // Data for Group create
        $data = ArrayHash::from([
            'label' => 'TEST_SUPER_GROUP_FIRST',
            'created' => DateTime::from($this->dateTimeStr),
            'userId' => 50
        ]);

        // Create group and test expected data
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('User not found.');
        $this->functionality->create($data);
    }

    public function testUpdate(): void
    {
        // Data for SuperGroup update
        $data = ArrayHash::from([
            'label' => 'TEST_SUPER_GROUP_UPDATE'
        ]);

        $expected = $this->firstSuperGroup;
        $expected->setLabel($data['label']);

        // Update SuperGroup and test expected data
        $updated = $this->functionality->update(1, $data);
        $this->assertEquals($expected, $updated);

        // Test non-valid update
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('SuperGroup for update not found.');
        $this->functionality->update(50, $data);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function testDelete(): void
    {
        // Try to delete, success expected
        $this->assertTrue($this->functionality->delete(1));

        // Try to delete, exception expected
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Entity for deletion was not found.');
        $this->functionality->delete(50);
    }

    /**
     * @throws EntityNotFoundException
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function testUpdatePermissions(): void
    {
        // Add Groups into tested SuperGroup
        $this->firstSuperGroup->setGroups(new ArrayCollection([$this->firstGroup, $this->secondGroup]));

        // Update SuperGroup permissions
        $this->functionality->updatePermissions(1, [1,2]);

        // Test SuperGroup expected values
        /** @var SuperGroup|null $superGroup */
        $superGroup = $this->superGroupRepositoryMock->find(1);
        $this->assertInstanceOf(SuperGroup::class, $superGroup);
        $this->assertEquals(2, $superGroup->getThemes()->count());
        $this->assertEquals([1, 2], $superGroup->getPropertyKeyArray('themes'));
        $this->assertEquals(new ArrayCollection([$this->firstTheme, $this->secondTheme]), $superGroup->getThemes());

        // Test firstGroup expected values
        $this->assertEquals(new ArrayCollection([$this->firstTheme, $this->secondTheme]), $this->firstGroup->getThemes());
        $this->assertEquals([1, 2], $this->firstGroup->getPropertyKeyArray('themes'));

        // Test secondGroup expected values
        $this->assertEquals(new ArrayCollection([$this->firstTheme, $this->secondTheme]), $this->secondGroup->getThemes());
        $this->assertEquals([1, 2], $this->secondGroup->getPropertyKeyArray('themes'));
    }
}
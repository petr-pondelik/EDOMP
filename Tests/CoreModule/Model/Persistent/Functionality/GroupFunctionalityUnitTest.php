<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.12.19
 * Time: 15:22
 */

namespace App\Tests\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\Group;
use App\CoreModule\Model\Persistent\Functionality\GroupFunctionality;
use App\Tests\MockTraits\Repository\GroupRepositoryMockTrait;
use App\Tests\MockTraits\Repository\SuperGroupRepositoryMockTrait;
use App\Tests\MockTraits\Repository\ThemeRepositoryMockTrait;
use App\Tests\MockTraits\Repository\UserRepositoryMockTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;

/**
 * Class GroupFunctionalityUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Functionality
 */
final class GroupFunctionalityUnitTest extends FunctionalityUnitTestCase
{
    use GroupRepositoryMockTrait;
    use SuperGroupRepositoryMockTrait;
    use ThemeRepositoryMockTrait;
    use UserRepositoryMockTrait;

    /**
     * @var GroupFunctionality
     */
    protected $functionality;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpGroupRepositoryMock();
        $this->setUpSuperGroupRepositoryMock();
        $this->setUpThemeRepositoryMock();
        $this->setUpUserRepositoryMock();
        $this->functionality = new GroupFunctionality($this->em, $this->groupRepositoryMock, $this->superGroupRepositoryMock, $this->themeRepositoryMock, $this->userRepositoryMock);
    }

    /**
     * @throws \Exception
     */
    public function testCreate(): void
    {
        // Data for Group create
        $data = ArrayHash::from([
            'label' => 'TEST_GROUP',
            'superGroup' => 1,
            'created' => DateTime::from($this->dateTimeStr),
            'userId' => 1
        ]);

        // Prepare expected data
        $expected = new Group();
        $expected->setLabel($data['label']);
        $expected->setSuperGroup($this->superGroupRepositoryMock->find($data['superGroup']));
        $expected->setCreatedBy($this->userRepositoryMock->find($data['userId']));
        $expected->setCreated(DateTime::from($this->dateTimeStr));

        // Create group and test expected data
        $created = $this->functionality->create($data);
        $this->assertEquals($expected, $created);
    }

    /**
     * @throws EntityNotFoundException
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function testCreateSuperGroupNotFound(): void
    {
        // Data for Group create
        $data = ArrayHash::from([
            'label' => 'TEST_GROUP',
            'superGroup' => 50,
            'created' => DateTime::from($this->dateTimeStr),
            'userId' => 1
        ]);

        // Create group and test expected data
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('SuperGroup not found.');
        $this->functionality->create($data);
    }

    /**
     * @throws EntityNotFoundException
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function testCreateUserNotFound(): void
    {
        // Data for Group create
        $data = ArrayHash::from([
            'label' => 'TEST_GROUP',
            'superGroup' => 1,
            'created' => DateTime::from($this->dateTimeStr),
            'userId' => 50
        ]);

        // Create group and test expected data
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('User not found.');
        $this->functionality->create($data);
    }

    /**
     * @throws \Exception
     */
    public function testUpdate(): void
    {
        // Data for Group update
        $data = ArrayHash::from([
            'label' => 'TEST_GROUP_UPDATE',
            'superGroup' => 2
        ]);

        $expected = new Group();
        $expected->setId(1);
        $expected->setLabel($data['label']);
        $expected->setSuperGroup($this->superGroupRepositoryMock->find($data['superGroup']));
        $expected->setCreated(DateTime::from($this->dateTimeStr));

        // Update group and test expected data
        $updated = $this->functionality->update(1, $data);
        $this->assertEquals($expected, $updated);

        // Test non-valid update
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Group for update not found.');
        $this->functionality->update(50, $data);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function testDelete(): void
    {
        $this->assertTrue($this->functionality->delete(1));
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
        // Update Group permissions and test expected values
        $this->functionality->updatePermissions(1, [1, 2]);
        /** @var Group|null $group */
        $group = $this->groupRepositoryMock->find(1);
        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals(2, $group->getThemes()->count());
        $this->assertEquals([1, 2], $group->getThemesId());
        $this->assertEquals(new ArrayCollection([$this->firstTheme, $this->secondTheme]), $group->getThemes());

    }
}
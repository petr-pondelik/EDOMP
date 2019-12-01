<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.12.19
 * Time: 11:15
 */

namespace App\Tests\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\SubTheme;
use App\CoreModule\Model\Persistent\Functionality\SubThemeFunctionality;
use App\Tests\MockTraits\Repository\SubThemeRepositoryMockTrait;
use App\Tests\MockTraits\Repository\ThemeRepositoryMockTrait;
use App\Tests\MockTraits\Repository\UserRepositoryMockTrait;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;

/**
 * Class SubThemeFunctionalityUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Functionality
 */
final class SubThemeFunctionalityUnitTest extends FunctionalityUnitTestCase
{
    use SubThemeRepositoryMockTrait;
    use ThemeRepositoryMockTrait;
    use UserRepositoryMockTrait;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpSubThemeRepositoryMock();
        $this->setUpThemeRepositoryMock();
        $this->setUpUserRepositoryMock();
        $this->functionality = new SubThemeFunctionality($this->em, $this->subThemeRepositoryMock, $this->themeRepositoryMock, $this->userRepositoryMock);
    }

    /**
     * @throws \Exception
     */
    public function testCreate(): void
    {
        // Data for SubCategory create
        $data = ArrayHash::from([
            'label' => 'TEST_SUB_THEME',
            'theme' => 1,
            'userId' => 2,
            'created' => $this->dateTimeStr
        ]);

        // Prepare expected SubTheme
        $expected = new SubTheme();
        $expected->setLabel($data['label']);
        $expected->setTheme($this->themeRepositoryMock->find($data['theme']));
        $expected->setCreatedBy($this->userRepositoryMock->find($data['userId']));
        $expected->setCreated(DateTime::from($this->dateTimeStr));

        // Create SubTheme and test expected data
        $created = $this->functionality->create($data);
        $this->assertEquals($expected, $created);
    }

    public function testCreateThemeNotFound(): void
    {
        // Data for SubCategory non-valid create
        $data = ArrayHash::from([
            'label' => 'TEST_SUB_THEME',
            'theme' => 50,
            'userId' => 2,
            'created' => $this->dateTimeStr
        ]);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Theme not found.');
        $this->functionality->create($data);
    }

    public function testCreateUserNotFound(): void
    {
        // Data for SubCategory non-valid create
        $data = ArrayHash::from([
            'label' => 'TEST_SUB_THEME',
            'theme' => 2,
            'userId' => 50,
            'created' => $this->dateTimeStr
        ]);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('User not found.');
        $this->functionality->create($data);
    }

    /**
     * @throws \Exception
     */
    public function testUpdate(): void
    {
        // Data for SubCategory update
        $data = ArrayHash::from([
            'label' => 'TEST_SUB_THEME_UPDATE',
            'theme' => 2
        ]);

        // Prepare expected Theme
        $expected = new SubTheme();
        $expected->setId(1);
        $expected->setLabel($data['label']);
        $expected->setCreated(DateTime::from($this->dateTimeStr));
        $expected->setTheme($this->themeRepositoryMock->find($data['theme']));

        // Update Theme and test it against expected
        $updated = $this->functionality->update(1, $data);
        $this->assertEquals($expected, $updated);

        // Test non-valid update
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('SubTheme for update not found.');
        $this->functionality->update(50, $data);
    }

    /**
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function testDelete(): void
    {
        $this->assertTrue($this->functionality->delete(1));
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Entity for deletion was not found.');
        $this->functionality->delete(50);
    }
}
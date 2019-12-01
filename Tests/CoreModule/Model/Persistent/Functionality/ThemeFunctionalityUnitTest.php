<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.11.19
 * Time: 17:36
 */

namespace App\Tests\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\Theme;
use App\CoreModule\Model\Persistent\Functionality\ThemeFunctionality;
use App\Tests\MockTraits\Repository\ThemeRepositoryMockTrait;
use App\Tests\MockTraits\Repository\UserRepositoryMockTrait;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;

/**
 * Class ThemeFunctionalityUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Functionality
 */
final class ThemeFunctionalityUnitTest extends FunctionalityUnitTestCase
{
    use ThemeRepositoryMockTrait;
    use UserRepositoryMockTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpThemeRepositoryMock();
        $this->setUpUserRepositoryMock();
        $this->functionality = new ThemeFunctionality($this->em, $this->themeRepositoryMock, $this->userRepositoryMock);
    }

    /**
     * @throws \Exception
     */
    public function testCreate(): void
    {
        // Data for Theme create
        $data = ArrayHash::from([
            'label' => 'TEST_THEME',
            'created' => $this->dateTimeStr,
            'userId' => 1
        ]);

        // Prepare expected Theme
        $expected = new Theme();
        $expected->setLabel($data['label']);
        $expected->setCreated(DateTime::from($data['created']));
        $expected->setCreatedBy($this->userRepositoryMock->find($data['userId']));

        // Create Theme and test it against expected
        $created = $this->functionality->create($data);
        $this->assertEquals($expected, $created);
    }

    public function testUpdate(): void
    {
        // Data for Theme update
        $data = ArrayHash::from([
            'label' => 'TEST_THEME_UPDATE'
        ]);

        // Prepare expected Theme
        $expected = new Theme();
        $expected->setId(1);
        $expected->setLabel($data['label']);
        $expected->setCreated(DateTime::from($this->dateTimeStr));

        // Update Theme and test it against expected
        $updated = $this->functionality->update(1, $data);
        $this->assertEquals($expected, $updated);
    }
}
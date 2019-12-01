<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.12.19
 * Time: 18:00
 */

namespace App\Tests\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Functionality\LogoFunctionality;
use App\Tests\MockTraits\Repository\LogoRepositoryMockTrait;
use App\Tests\MockTraits\Repository\UserRepositoryMockTrait;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;

/**
 * Class LogoFunctionalityUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Functionality
 */
final class LogoFunctionalityUnitTest extends FunctionalityUnitTestCase
{
    use LogoRepositoryMockTrait;
    use UserRepositoryMockTrait;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpLogoRepositoryMock();
        $this->setUpUserRepositoryMock();
        $this->functionality = new LogoFunctionality($this->em, $this->logoRepositoryMock, $this->userRepositoryMock);
    }

    public function testCreate(): void
    {
        // Data for Group create
        $data = ArrayHash::from([
            'label' => 'TEST_FIRST_LOGO',
            'extensionTmp' => '.pdf',
            'created' => DateTime::from($this->dateTimeStr),
            'createdBy' => 1
        ]);

        $expected = $this->firstLogo;
        $expected->setCreatedBy($this->userRepositoryMock->find($data['createdBy']));
        $expected->setExtensionTmp($data['extensionTmp']);

        $created = $this->functionality->create($data);
        $created->setId(1);

        $this->assertEquals($expected, $created);
    }

    public function testCreateUserNotFound(): void
    {
        // Data for Group create
        $data = ArrayHash::from([
            'label' => 'TEST_LOGO',
            'extensionTmp' => '.pdf',
            'created' => DateTime::from($this->dateTimeStr),
            'createdBy' => 50
        ]);

        // Create group and test expected data
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('User not found.');
        $this->functionality->create($data);
    }

    public function testUpdate(): void
    {
        // Data for Logo update
        $data = ArrayHash::from([
            'label' => 'TEST_LOGO_UPDATE',
            'extensionTmp' => 'TEST_EXTENSION_TMP_UPDATE',
            'extension' => 'TEST_EXTENSION_UPDATE',
            'path' => 'TEST_PATH_UPDATE',
        ]);

        $expected = $this->firstLogo;
        $expected->setId(1);
        foreach ($data as $key => $item) {
            $expected->{'set' . Strings::firstUpper($key)}($item);
        }

        // Update Logo and test it against expected data
        $updated = $this->functionality->update(1, $data);
        $this->assertEquals($expected, $updated);

        // Test non-valid update
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Logo for update not found.');
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
}
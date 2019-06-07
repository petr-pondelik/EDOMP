<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.6.19
 * Time: 17:01
 */

namespace App\AppTests\Model\Functionality;

use App\Model\Entity\Logo;
use App\Model\Functionality\LogoFunctionality;
use App\Model\Repository\LogoRepository;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;

/**
 * Class LogoFunctionalityTest
 * @package App\AppTests\Model\Functionality
 */
class LogoFunctionalityTest extends FunctionalityTestCase
{
    /**
     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        parent::setUp();

        // Mock the LogoRepository
        $this->repositoryMock = $this->getMockBuilder(LogoRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Instantiate tested class
        $this->functionality = new LogoFunctionality($this->em, $this->repositoryMock);
    }

    /**
     * @throws \Exception
     */
    public function testFunctionality(): void
    {
        // Data for Category create
        $data = ArrayHash::from([
            'extension_tmp' => 'TEST_EXTENSION_TMP'
        ]);

        // Create Logo and test expected data
        $logo = $this->functionality->create($data);
        $this->assertInstanceOf(Logo::class, $logo);
        $this->assertEquals($data->extension_tmp, $logo->getExtensionTmp());

        // Set repository expected return values for find
        $this->repositoryMock->expects($this->atLeastOnce())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($logo) {
                $map = [
                    1 => $logo,
                    50 => null
                ];
                return $map[$arg];
            });

        // Data for Logo update
        $data = ArrayHash::from([
            'label' => 'TEST_LOGO',
            'extension_tmp' => 'NEW_TEST_EXTENSION_TMP',
            'extension' => 'TEST_EXTENSION',
            'path' => 'TEST_PATH',
        ]);

        // Update Category and test expected data
        $logo = $this->functionality->update(1, $data);
        $this->assertInstanceOf(Logo::class, $logo);
        $this->assertEquals($logo->getLabel(), 'TEST_LOGO');
        $this->assertEquals($logo->getExtensionTmp(), 'NEW_TEST_EXTENSION_TMP');
        $this->assertEquals($logo->getExtension(), 'TEST_EXTENSION');
        $this->assertEquals($logo->getPath(), 'TEST_PATH');

        // Try to delete, success expected
        $this->assertTrue($this->functionality->delete(1));

        // Try to delete, exception expected
        $this->expectException(EntityNotFoundException::class);
        $this->functionality->delete(50);
    }
}
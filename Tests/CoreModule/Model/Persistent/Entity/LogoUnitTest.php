<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.11.19
 * Time: 18:31
 */

namespace App\Tests\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Entity\Logo;
use App\Tests\Traits\UserMockSetUpTrait;

/**
 * Class LogoUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
class LogoUnitTest extends PersistentEntityTestCase
{
    use UserMockSetUpTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpUserMock();
    }

    protected $errorMessages = [
        "Label can't be blank.",
        'ExtensionTmp can\'t be blank.'
    ];

    public function testValidState(): void
    {
        $entity = new Logo();
        $extension = '.pdf';
        $label = 'TEST_LABEL';
        $path = 'TEST_PATH';

        $this->assertInstanceOf(Logo::class, $entity);

        $entity->setExtension($extension);
        $entity->setExtensionTmp($extension);
        $entity->setCreatedBy($this->userMock);
        $entity->setLabel($label);
        $entity->setPath($path);

        $this->assertTrue($entity->isTeacherLevelSecured());
        $this->assertEquals($extension, $entity->getExtension());
        $this->assertEquals($extension, $entity->getExtensionTmp());
        $this->assertEquals($label, $entity->getLabel());
        $this->assertEquals($path, $entity->getPath());

        $this->assertValidByValidator($entity);
    }

    public function testInvalidState(): void
    {
        $entity = new Logo();
        $this->assertValidatorViolations($entity);
    }
}
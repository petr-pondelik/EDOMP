<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.6.19
 * Time: 21:53
 */

namespace Tests\Model\Entity;


use App\Model\Entity\Logo;

/**
 * Class LogoTest
 * @package AppTests\Model\Entity
 */
class LogoTest extends EntityTestCase
{
    /**
     * @throws \Exception
     */
    public function testValues(): void
    {
        $entity = new Logo();
        $entity->setLabel('TEST_LOGO');
        $entity->setExtensionTmp('TEST_EXTENSION_TMP');
        $entity->setExtension('TEST_EXTENSION');
        $entity->setPath('TEST_PATH');

        $this->assertEquals($entity->getLabel(), 'TEST_LOGO');
        $this->assertEquals($entity->getExtensionTmp(), 'TEST_EXTENSION_TMP');
        $this->assertEquals($entity->getExtension(), 'TEST_EXTENSION');
        $this->assertEquals($entity->getPath(), 'TEST_PATH');
        $this->assertEquals($entity->isUsed(), false);
    }

    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $entity = new Logo();
        $entity->setLabel('TEST_LOGO');
        $entity->setExtensionTmp('EXTENSION_TMP');

        $this->assertInstanceOf(Logo::class, $entity);
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 0);
    }

    /**
     * @throws \Exception
     */
    public function testCreateError(): void
    {
        $errorMsgs = [
            0 => "ExtensionTmp can't be blank.",
            1 => "Label can't be blank."
        ];

        $entity = new Logo();
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 2);

        foreach ($errors as $key => $error){
            $this->assertEquals($error->getMessage(), $errorMsgs[$key]);
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.6.19
 * Time: 21:53
 */

namespace AppTests\Model\Entity;


use App\AppTests\Entity\EntityTestCase;
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
    public function testCreateSuccess(): void
    {
        $entity = new Logo();
        $entity->setLabel("TESTLOGO");
        $entity->setExtensionTmp("EXTENSION_TMP");
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

        foreach ($errors as $key => $error)
            $this->assertEquals($error->getMessage(), $errorMsgs[$key]);
    }
}
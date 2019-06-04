<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.6.19
 * Time: 22:29
 */

namespace AppTests\Model\Entity;


use App\AppTests\Entity\EntityTestCase;
use App\Model\Entity\TemplateJsonData;

/**
 * Class TemplateJsonDataTest
 * @package AppTests\Model\Entity
 */
class TemplateJsonDataTest extends EntityTestCase
{
    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $entity = new TemplateJsonData();
        $entity->setTemplateId(5);
        $this->assertInstanceOf(TemplateJsonData::class, $entity);
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 0);
    }

    /**
     * @throws \Exception
     */
    public function testCreateError(): void
    {
        $errorMsgs = [
            0 => "TemplateId can't be blank.",
        ];

        $entity = new TemplateJsonData();
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 1);

        foreach ($errors as $key => $error)
            $this->assertEquals($errors->get($key)->getMessage(), $errorMsgs[$key]);
    }
}
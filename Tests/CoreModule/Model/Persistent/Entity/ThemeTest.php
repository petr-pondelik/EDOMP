<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.11.19
 * Time: 22:32
 */

namespace App\Tests\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Entity\Theme;

/**
 * Class CategoryTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
class ThemeTest extends PersistentEntityTestCase
{
    public function testValidState(): void
    {
        $entity = new Theme();
        $entity->setLabel('TEST_LABEL');
        $entity->setCreatedBy();
        $entity->setGroups();
        $entity->setSubThemes();
        $entity->setSuperGroups();
    }

    public function testInvalidState(): void
    {
        $entity = new Theme();
    }
}
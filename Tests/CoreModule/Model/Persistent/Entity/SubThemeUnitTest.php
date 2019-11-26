<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.11.19
 * Time: 1:04
 */

namespace App\Tests\CoreModule\Model\Persistent\Entity;


use App\CoreModule\Model\Persistent\Entity\SubTheme;
use App\Tests\Traits\ProblemFinalMockSetUpTrait;
use App\Tests\Traits\ThemeMockSetUpTrait;

/**
 * Class SubThemeUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
class SubThemeUnitTest extends PersistentEntityTestCase
{
    use ProblemFinalMockSetUpTrait;
    use ThemeMockSetUpTrait;


    public function testValidState(): void
    {
        $entity = new SubTheme();
        var_dump($entity);
        $entity->setLabel('TEST_LABEL');

    }

    public function testInvalidState(): void
    {

    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.11.19
 * Time: 1:08
 */

namespace App\Tests\Traits;

use App\CoreModule\Model\Persistent\Entity\Theme;

/**
 * Trait ThemeMockSetUpTrait
 * @package App\Tests\Traits
 */
trait ThemeMockSetUpTrait
{
    /**
     * @var Theme
     */
    protected $themeMock;

    protected function setUpThemeMock(): void
    {
        $this->themeMock = $this->getMockBuilder(Theme::class)->disableOriginalConstructor()->getMock();
    }
}
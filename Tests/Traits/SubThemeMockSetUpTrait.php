<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.11.19
 * Time: 22:57
 */

namespace App\Tests\Traits;

use App\CoreModule\Model\Persistent\Entity\SubTheme;

/**
 * Trait SubThemeMockSetUpTrait
 * @package App\Tests\Traits
 */
trait SubThemeMockSetUpTrait
{
    /**
     * @var SubTheme
     */
    protected $subThemeMock;

    public function setUpSubThemeMock(): void
    {
        $this->subThemeMock = $this->getMockBuilder(SubTheme::class)->disableOriginalConstructor()->getMock();
    }
}
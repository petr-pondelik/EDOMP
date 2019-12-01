<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.11.19
 * Time: 17:51
 */

namespace App\Tests\MockTraits\Entity;

use App\CoreModule\Model\Persistent\Entity\Logo;

/**
 * Trait LogoMockSetUpTrait
 * @package App\Tests\Traits
 */
trait LogoMockSetUpTrait
{
    /**
     * @var Logo
     */
    protected $logoMock;

    protected function setUpLogoMock(): void
    {
        $this->logoMock = $this->getMockBuilder(Logo::class)->disableOriginalConstructor()->getMock();
    }
}
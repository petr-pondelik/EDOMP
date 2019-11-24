<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 18:54
 */

namespace App\CoreModule\Model\Persistent\Traits;

use App\CoreModule\Model\Persistent\Entity\Logo;

/**
 * Trait ToStringTrait
 * @package App\CoreModule\Model\Persistent\Traits
 */
trait ToStringTrait
{
    /**
     * @return string
     */
    public function __toString(): string
    {
        if($this instanceof Logo){
            return "<img src='" . $this->{$this->toStringAttr} . "'/>";
        }
        return $this->{$this->toStringAttr};
    }
}
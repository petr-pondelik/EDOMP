<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 18:54
 */

namespace App\Model\Persistent\Traits;

use App\Model\Persistent\Entity\Logo;

/**
 * Trait ToStringTrait
 * @package App\Model\Persistent\Traits
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
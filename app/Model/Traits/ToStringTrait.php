<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 18:54
 */

namespace App\Model\Traits;

use App\Model\Entity\Logo;
use App\Model\Entity\Role;
use App\Model\Entity\Term;

/**
 * Trait ToStringTrait
 * @package App\Model\Traits
 */
trait ToStringTrait
{
    /**
     * @return string
     */
    public function __toString(): string
    {
        if($this instanceof Logo)
            return "<img src='" . $this->{$this->toStringAttr} . "'/>";
        return $this->{$this->toStringAttr};
    }
}
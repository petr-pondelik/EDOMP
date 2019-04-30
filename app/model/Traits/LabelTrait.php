<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 18:54
 */

namespace App\Model\Traits;

/**
 * Trait LabelTrait
 * @package App\Model\Traits
 */
trait LabelTrait
{
    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->body;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.8.19
 * Time: 19:23
 */

namespace App\Model\NonPersistent\Traits;

use Nette\Utils\ArrayHash;

/**
 * Trait SetValuesTrait
 * @package App\Model\NonPersistent\Traits
 */
trait SetValuesTrait
{
    /**
     * @param ArrayHash $values
     */
    protected function setValues(ArrayHash $values): void
    {
        foreach ($values as $key => $value){
            if(property_exists(static::class, $key)){
                $this->{$key} = $value;
            }
        }
    }
}
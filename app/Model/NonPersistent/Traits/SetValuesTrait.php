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
    public function setValues(ArrayHash $values): void
    {
        bdump('SET VALUES');
        bdump($values);
        foreach ($values as $key => $value){
            if(property_exists(static::class, $key)){
                $this->{$key} = $value;
            }
        }
    }

    /**
     * @param ArrayHash $values
     */
    public function completeValues(ArrayHash $values): void
    {
        bdump('COMPLETE VALUES');
        bdump($values);
        foreach ($values as $key => $value){
            if(property_exists(static::class, $key) && $this->{$key} === null){
                $this->{$key} = $value;
            }
        }
    }
}
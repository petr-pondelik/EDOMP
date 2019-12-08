<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.8.19
 * Time: 19:23
 */

namespace App\TeacherModule\Model\NonPersistent\Traits;

/**
 * Trait SetValuesTrait
 * @package App\TeacherModule\Model\NonPersistent\Traits
 */
trait SetValuesTrait
{
    /**
     * @param iterable $values
     */
    public function setValues(iterable $values): void
    {
        bdump('SET VALUES');
        foreach ($values as $key => $value) {
            if (property_exists(static::class, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * @param iterable $values
     */
    public function completeValues(iterable $values): void
    {
        bdump('COMPLETE VALUES');
        foreach ($values as $key => $value) {
            if (property_exists(static::class, $key) && $this->{$key} === null) {
                $this->{$key} = $value;
            }
        }
    }
}
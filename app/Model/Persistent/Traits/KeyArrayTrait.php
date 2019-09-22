<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.9.19
 * Time: 15:25
 */

namespace App\Model\Persistent\Traits;

use App\Exceptions\EntityException;
use Doctrine\Common\Collections\Collection;

/**
 * Trait KeyArrayTrait
 * @package App\Model\Persistent\Traits
 */
trait KeyArrayTrait
{
    /**
     * @param string $property
     * @return array
     * @throws EntityException
     */
    public function getPropertyKeyArray(string $property): array
    {
        bdump($this->{$property});

        if(!property_exists(static::class, $property)){
            throw new EntityException('Property does not exist.');
        }
        if(!($this->{$property} instanceof Collection)){
            throw new EntityException('Property does not implement Doctrine Collection interface.');
        }

        $res = [];

        foreach ($this->{$property}->getValues() as $key => $item){
            $res[] = $item->getId();
        }

        return $res;
    }
}
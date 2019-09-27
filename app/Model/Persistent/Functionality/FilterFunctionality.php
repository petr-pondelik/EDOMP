<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.9.19
 * Time: 17:30
 */

namespace App\Model\Persistent\Functionality;

use App\Model\Persistent\Entity\BaseEntity;
use App\Model\Persistent\Entity\Filter;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class FilterFunctionality
 * @package App\Model\Persistent\Functionality
 */
class FilterFunctionality extends BaseFunctionality
{

    /**
     * @param ArrayHash $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\Exceptions\EntityException
     * @throws \ReflectionException
     */
    public function create(ArrayHash $data, bool $flush = true): ?BaseEntity
    {
        bdump('CREATE FILTER FUNCTIONALITY');
        $entity = new Filter();
        $reflection = new \ReflectionClass(Filter::class);
        foreach ($reflection->getProperties() as $property) {
            $propertyName = $property->getName();
            if (isset($data->{$propertyName})) {
                $entity->{'set' . Strings::firstUpper($propertyName)}($data->{$propertyName});
            }
        }
        $this->em->persist($entity);
        if ($flush) {
            $this->em->flush();
        }
        return $entity;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @param bool $flush
     * @return BaseEntity|null
     */
    public function update(int $id, ArrayHash $data, bool $flush = true): ?BaseEntity
    {
        return null;
    }
}
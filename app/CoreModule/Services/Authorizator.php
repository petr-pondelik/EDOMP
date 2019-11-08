<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 15.4.19
 * Time: 12:40
 */

namespace App\Services;


use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use Nette\Security\IAuthorizator;
use Nette\Security\IIdentity;

/**
 * Class Authorizator
 * @package App\Services
 */
class Authorizator implements IAuthorizator
{
    /**
     * Performs a role-based authorization.
     * @param  string|null
     * @param  string|null
     * @param  string|null
     * @return bool
     */
    public function isAllowed($role, $resource, $privilege): bool
    {
        return true;
    }

    /**
     * @param IIdentity $userIdentity
     * @param int $categoryId
     * @return bool
     */
    public function isCategoryAllowed(IIdentity $userIdentity, int $categoryId): bool
    {
        foreach ($userIdentity->categories as $key => $category){
            if($key === $categoryId){
                return true;
            }
        }
        return false;
    }

    /**
     * @param IIdentity $user
     * @param BaseEntity $entity
     * @return bool
     */
    public function isEntityAllowed(IIdentity $user, BaseEntity $entity): bool
    {
        if($createdBy = $entity->getCreatedBy()){
            return $user->getId() === $createdBy->getId();
        }
        return false;
    }
}
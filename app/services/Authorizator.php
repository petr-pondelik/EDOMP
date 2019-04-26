<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 15.4.19
 * Time: 12:40
 */

namespace App\Services;


use Nette\Security\IAuthorizator;
use Nette\Security\IIdentity;

class Authorizator implements IAuthorizator
{
    /**
     * Performs a role-based authorization.
     * @param  string|null
     * @param  string|null
     * @param  string|null
     * @return bool
     */
    function isAllowed($role, $resource, $privilege)
    {
        // TODO: Implement isAllowed() method.
        return true;
    }

    /**
     * @param IIdentity $userIdentity
     * @param int $categoryId
     * @return bool
     */
    public function isCategoryAllowed(IIdentity $userIdentity, int $categoryId): bool
    {
        foreach ($userIdentity->categories as $category){
            if($category->category_id === $categoryId)
                return true;
        }
        return false;
    }
}
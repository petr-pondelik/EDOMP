<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 15.4.19
 * Time: 12:40
 */

namespace App\CoreModule\Services;


use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use Nette\Security\IAuthorizator;
use Nette\Security\IIdentity;
use Nette\Security\User;

/**
 * Class Authorizator
 * @package App\CoreModule\Services
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
     * @param User $user
     * @param int $themeId
     * @return bool
     */
    public function isThemeAllowed(User $user, int $themeId): bool
    {
        foreach ($user->identity->themes as $key => $theme) {
            if ($key === $themeId) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param User $user
     * @param BaseEntity $entity
     * @return bool
     */
    public function isEntityAllowed(User $user, BaseEntity $entity): bool
    {
        // If the user has admin role, entity is always allowed
        if ($user->isInRole('admin')) {
            return true;
        }

        // If the user has teacher role
        if ($user->isInRole('teacher')) {
            // If the entity is not teacher-level secured, it's allowed
            if (!$entity->isTeacherLevelSecured()) {
                return true;
            }
            // If the entity is teacher-level secured, it's allowed only for it's author
            if (method_exists($entity, 'getCreatedBy')) {
                $createdBy = $entity->getCreatedBy();
                return $createdBy ? $createdBy->getId() === $user->getId() : false;
            }
        }

        return false;
    }
}
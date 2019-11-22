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
     * @param IIdentity $userIdentity
     * @param int $themeId
     * @return bool
     */
    public function isThemeAllowed(IIdentity $userIdentity, int $themeId): bool
    {
        foreach ($userIdentity->themes as $key => $theme) {
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
        bdump('IS ENTITY ALLOWED');
        // If the user has admin role, entity is always allowed
        if ($user->isInRole('admin')) {
            return true;
        }

        // If the user has teacher role
        if ($user->isInRole('teacher')) {
            bdump('TEACHER ROLE');
            // If the entity is not teacher-level secured, it's allowed
            if (!$entity->isTeacherLevelSecured()) {
                bdump('NOT TEACHER LEVEL SECURED');
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
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.11.19
 * Time: 15:15
 */

namespace App\CoreModule\Interfaces;

use Nette\Security\User;

/**
 * Interface ISecuredRepository
 * @package App\CoreModule\Interfaces
 */
interface ISecuredRepository
{
    /**
     * @param User $user
     */
    public function findAllowed(User $user);
}
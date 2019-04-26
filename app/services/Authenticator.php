<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 20.3.19
 * Time: 23:43
 */

namespace App\Services;


use App\Model\Managers\UserManager;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;

/**
 * Class Authenticator
 * @package App\Services
 */
class Authenticator implements IAuthenticator
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * Authenticator constructor.
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param array $credentials
     * @return Identity|IIdentity
     * @throws AuthenticationException
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    function authenticate(array $credentials)
    {
        list($username, $password) = $credentials;
        $user = $this->userManager->getForAuthentication($username);
        bdump($user);

        if(!$user || !Passwords::verify($password, $user->password))
            throw new AuthenticationException('Zadáno neplatné uživatelské jméno nebo heslo.');

        $roles = $this->userManager->getRoles((int) $user->user_id);

        if(!in_array("admin", $roles))
            $categories = $this->userManager->getCategories((int) $user->user_id);
        else
            $categories = $this->userManager->getCategories();

        return new Identity($user->user_id, $roles, [
            "username" => $user->username,
            "categories" => $categories
        ]);
    }

}
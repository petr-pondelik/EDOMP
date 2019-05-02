<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 20.3.19
 * Time: 23:43
 */

namespace App\Service;

use App\Model\Repository\CategoryRepository;
use App\Model\Repository\UserRepository;
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
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * Authenticator constructor.
     * @param UserRepository $userRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(UserRepository $userRepository, CategoryRepository $categoryRepository)
    {
        $this->userRepository = $userRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param array $credentials
     * @return Identity|IIdentity
     * @throws AuthenticationException
     */
    function authenticate(array $credentials)
    {
        list($username, $password) = $credentials;
        $user = $this->userRepository->findOneBy([
            "username" => $username
        ]);
        bdump($user);

        if(!$user || !Passwords::verify($password, $user->getPassword()))
            throw new AuthenticationException('Zadáno neplatné uživatelské jméno nebo heslo.');

        $roles = $user->getRolesLabel();

        bdump($roles);

        if(!in_array("admin", $roles))
            $categories = $this->userManager->getCategories((int) $user->user_id);
        else
            $categories = $this->categoryRepository->findPairs([], "label");

        return new Identity($user->getId(), $roles, [
            "username" => $user->getUsername(),
            "categories" => $categories
        ]);
    }

}
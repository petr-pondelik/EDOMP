<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 20.3.19
 * Time: 23:43
 */

namespace App\Services;

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
    public function authenticate(array $credentials)
    {
        [$username, $password] = $credentials;
        $user = $this->userRepository->findOneBy([
            'username' => $username
        ]);

        if(!$user || !Passwords::verify($password, $user->getPassword())){
            throw new AuthenticationException('Zadáno neplatné uživatelské jméno nebo heslo.');
        }

        $role = $user->getRole();

        $categories = [];

        if(!strcmp('student', $role->getKey())){
            $categoryIds = $user->getCategoriesId();
            foreach ($categoryIds as $categoryId){
                $categories[$categoryId] = $this->categoryRepository->find($categoryId)->getLabel();
            }
        }
        else{
            $categories = $this->categoryRepository->findPairs([], 'label');
        }

        return new Identity($user->getId(), $role->getKey(), [
            'username' => $user->getUsername(),
            'categories' => $categories,
            'roleLabel' => $role->getLabel()
        ]);
    }

}
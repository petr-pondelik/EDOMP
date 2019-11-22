<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 20.3.19
 * Time: 23:43
 */

namespace App\CoreModule\Services;

use App\CoreModule\Model\Persistent\Repository\ThemeRepository;
use App\CoreModule\Model\Persistent\Repository\UserRepository;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;

/**
 * Class Authenticator
 * @package App\CoreModule\Services
 */
class Authenticator implements IAuthenticator
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var ThemeRepository
     */
    protected $themeRepository;

    /**
     * Authenticator constructor.
     * @param UserRepository $userRepository
     * @param ThemeRepository $themeRepository
     */
    public function __construct(UserRepository $userRepository, ThemeRepository $themeRepository)
    {
        $this->userRepository = $userRepository;
        $this->themeRepository = $themeRepository;
    }

    /**
     * @param array $credentials
     * @return Identity|IIdentity
     * @throws AuthenticationException
     */
    public function authenticate(array $credentials)
    {
        [$username, $password, $admin] = $credentials;

        if (!$admin) {
            $user = $this->userRepository->findOneBy([
                'username' => $username
            ]);
        } else {
            $user = $this->userRepository->findOneBy([
                'username' => $username,
                'role' => [1, 2]
            ]);
        }

        if (!$user || !Passwords::verify($password, $user->getPassword())) {
            throw new AuthenticationException('Zadáno neplatné uživatelské jméno nebo heslo.');
        }

        $role = $user->getRole();

        $themes = [];

        if (!strcmp('student', $role->getKey())) {
            $themeIds = $user->getThemesId();
            foreach ($themeIds as $themeId) {
                $themes[$themeId] = $this->themeRepository->find($themeId)->getLabel();
            }
        } else {
            $themes = $this->themeRepository->findPairs([], 'label');
        }

        return new Identity($user->getId(), $role->getKey(), [
            'username' => $user->getUsername(),
            'themes' => $themes,
            'roleLabel' => $role->getLabel(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName()
        ]);
    }

}
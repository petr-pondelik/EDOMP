<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 20.3.19
 * Time: 23:43
 */

namespace App\CoreModule\Services;

use App\CoreModule\Helpers\ConstHelper;
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
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * Authenticator constructor.
     * @param UserRepository $userRepository
     * @param ThemeRepository $themeRepository
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        UserRepository $userRepository,
        ThemeRepository $themeRepository,
        ConstHelper $constHelper
    )
    {
        $this->userRepository = $userRepository;
        $this->themeRepository = $themeRepository;
        $this->constHelper = $constHelper;
    }

    /**
     * @param array $credentials
     * @return Identity|IIdentity
     * @throws AuthenticationException
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function authenticate(array $credentials)
    {
        [$username, $password, $admin] = $credentials;

        if (!$admin) {
            $user = $this->userRepository->findForAuthentication($username);
        } else {
            $user = $this->userRepository->findForAuthentication($username, [$this->constHelper::ADMIN_ROLE, $this->constHelper::TEACHER_ROLE]);
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
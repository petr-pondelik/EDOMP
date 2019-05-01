<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.5.19
 * Time: 22:10
 */

namespace App\Model\Functionality;

use App\Model\Entity\User;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\RoleRepository;
use App\Model\Repository\UserRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;

/**
 * Class UserFunctionality
 * @package App\Model\Functionality
 */
class UserFunctionality extends BaseFunctionality
{
    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * UserFunctionality constructor.
     * @param EntityManager $entityManager
     * @param UserRepository $userRepository
     * @param RoleRepository $roleRepository
     * @param GroupRepository $groupRepository
     */
    public function __construct
    (
        EntityManager $entityManager, UserRepository $userRepository,
        RoleRepository $roleRepository, GroupRepository $groupRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param ArrayHash $data
     * @return int
     * @throws \Exception
     */
    public function create(ArrayHash $data): int
    {
        bdump($data);
        $user = new User();
        $user->setUsername($data->username);
        $user->setPassword(Passwords::hash($data->password));
        foreach ($data->roles as $role)
            $user->addRole($this->roleRepository->find($role));
        foreach ($data->groups as $group)
            $user->addGroup($this->groupRepository->find($group));
        $this->em->persist($user);
        $this->em->flush();
        return $user->getId();
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @return Object
     */
    public function update(int $id, ArrayHash $data): ?Object
    {
        // TODO: Implement update() method.
    }
}
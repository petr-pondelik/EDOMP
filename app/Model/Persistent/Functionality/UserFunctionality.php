<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.5.19
 * Time: 22:10
 */

namespace App\Model\Persistent\Functionality;

use App\Model\Persistent\Entity\BaseEntity;
use App\Model\Persistent\Entity\User;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\GroupRepository;
use App\Model\Persistent\Repository\RoleRepository;
use App\Model\Persistent\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;

/**
 * Class UserFunctionality
 * @package App\Model\Persistent\Functionality
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
     * @param ConstraintEntityManager $entityManager
     * @param UserRepository $userRepository
     * @param RoleRepository $roleRepository
     * @param GroupRepository $groupRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager, UserRepository $userRepository,
        RoleRepository $roleRepository, GroupRepository $groupRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\Exceptions\EntityException
     */
    public function create(iterable $data, bool $flush = true): ?BaseEntity
    {
        bdump('CREATE USER');
        bdump($data);
        $user = new User();

        $user->setEmail($data->email);

        if (!isset($data->username) || !$data->username) {
            $user->setUsername($data->email);
        } else {
            $user->setUsername($data->username);
        }

        $user->setPassword($data->password);
        $user->setFirstName($data->firstName);
        $user->setLastName($data->lastName);
        $user->setRole($this->roleRepository->find($data->role));
        $user = $this->attachGroups($user, $data->groups);

        if (isset($data->userId)) {
            $user->setCreatedBy($this->repository->find($data->userId));
        }

        bdump($user);

        $this->em->persist($user);

        if ($flush) {
            $this->em->flush();
        }

        return $user;
    }

    /**
     * @param int $id
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws EntityNotFoundException
     * @throws \App\Exceptions\EntityException
     */
    public function update(int $id, iterable $data, bool $flush = true): ?BaseEntity
    {
        $user = $this->repository->find($id);

        if (!$user) {
            throw new EntityNotFoundException('Entity for update not found.');
        }

        $user->setEmail($data->email);

        if (!isset($data->username) || !$data->username) {
            $user->setUsername($data->email);
        } else {
            $user->setUsername($data->username);
        }

        $user->setFirstName($data->firstName);
        $user->setLastName($data->lastName);

        if (isset($data->role)) {
            $user->setRole($this->roleRepository->find($data->role));
        }

        if (isset($data->groups)) {
            $user->setGroups(new ArrayCollection());
            $user = $this->attachGroups($user, $data->groups);
        }

        $this->em->persist($user);

        if ($flush) {
            $this->em->flush();
        }

        return $user;
    }

    /**
     * @param User $user
     * @param array $groups
     * @return User
     */
    protected function attachGroups(User $user, array $groups): User
    {
        foreach ($groups as $group) {
            $user->addGroup($this->groupRepository->find($group));
        }
        return $user;
    }

    /**
     * @param int $id
     * @param string $password
     * @param bool $flush
     * @return User
     * @throws EntityNotFoundException
     * @throws \App\Exceptions\EntityException
     */
    public function updatePassword(int $id, string $password, $flush = true): User
    {
        $user = $this->repository->find($id);
        if (!$user) {
            throw new EntityNotFoundException('Entity for update not found.');
        }
        $user->setPassword($password);
        $this->em->persist($user);
        if ($flush) {
            $this->em->flush();
        }
        return $user;
    }
}
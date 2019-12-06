<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.5.19
 * Time: 22:10
 */

namespace App\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Entity\Group;
use App\CoreModule\Model\Persistent\Entity\Role;
use App\CoreModule\Model\Persistent\Entity\User;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\GroupRepository;
use App\CoreModule\Model\Persistent\Repository\RoleRepository;
use App\CoreModule\Model\Persistent\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\DateTime;

/**
 * Class UserFunctionality
 * @package App\CoreModule\Model\Persistent\Functionality
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
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws EntityNotFoundException
     */
    public function create(iterable $data, bool $flush = true): ?BaseEntity
    {
        bdump('CREATE USER');
        $user = new User();

        $user->setEmail($data['email']);

        if (!isset($data['username']) || !$data['username']) {
            $user->setUsername($data['email']);
        } else {
            $user->setUsername($data['username']);
        }

        $user->setPassword($data['password']);
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);

        /** @var Role|null $role */
        $role = $this->roleRepository->find($data['role']);
        if (!$role) {
            throw new EntityNotFoundException('Role not found.');
        }
        $user->setRole($role);

        $user = $this->attachGroups($user, $data['groups']);

        if (isset($data['userId'])) {
            /** @var User|null $createdBy */
            $createdBy = $this->repository->find($data['userId']);
            if (!$user) {
                throw new EntityNotFoundException('User not found.');
            }
            $user->setCreatedBy($createdBy);
        }

        if (isset($data['created'])) {
            $user->setCreated(DateTime::from($data['created']));
        }

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
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function update(int $id, iterable $data, bool $flush = true): ?BaseEntity
    {
        /** @var User|null $user */
        $user = $this->repository->find($id);

        if (!$user) {
            throw new EntityNotFoundException('Entity for update not found.');
        }

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        if (isset($data['password'])) {
            $user->setPassword($data['password']);
        }

        if (!isset($data['username']) || !$data['username']) {
            $user->setUsername($data['email']);
        } else {
            $user->setUsername($data['username']);
        }

        if (isset($data['firstName'])) {
            $user->setFirstName($data['firstName']);
        }

        if (isset($data['lastName'])) {
            $user->setLastName($data['lastName']);
        }

        if (isset($data['role'])) {
            /** @var Role|null $role */
            $role = $this->roleRepository->find($data['role']);
            if (!$role) {
                throw new EntityNotFoundException('Role not found.');
            }
            $user->setRole($role);
        }

        if (isset($data['groups'])) {
            $user->setGroups(new ArrayCollection());
            $user = $this->attachGroups($user, $data['groups']);
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
     * @throws EntityNotFoundException
     */
    protected function attachGroups(User $user, array $groups): User
    {
        foreach ($groups as $group) {
            /** @var Group|null $group */
            $group = $this->groupRepository->find($group);
            if (!$group) {
                throw new EntityNotFoundException('Group not found.');
            }
            $user->addGroup($group);
        }
        return $user;
    }

    /**
     * @param int $id
     * @param string $password
     * @param bool $flush
     * @return User
     * @throws EntityNotFoundException
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function updatePassword(int $id, string $password, $flush = true): User
    {
        /** @var User|null $user */
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

    /**
     * @param string $email
     * @param string $password
     * @param bool $flush
     * @return User
     * @throws EntityNotFoundException
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function updatePasswordByEmail(string $email, string $password, $flush = true): User
    {
        /** @var User|null $user */
        $user = $this->repository->findOneBy(['email' => $email]);

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
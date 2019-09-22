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
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;

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
     * @param ArrayHash $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\Exceptions\EntityException
     */
    public function create(ArrayHash $data, bool $flush = true): ?BaseEntity
    {
        $user = new User();
        $user->setUsername($data->username);
        $user->setPassword($data->password);
        $user->setFirstName($data->firstName);
        $user->setLastName($data->lastName);
        $user->setRole($this->roleRepository->find($data->role));
        $user = $this->attachGroups($user, $data->groups);
        if(isset($data->userId)){
            $user->setCreatedBy($this->repository->find($data->userId));
        }
        if(isset($data->created)){
            $user->setCreated($data->created);
        }
        $this->em->persist($user);
        if ($flush) {
            $this->em->flush();
        }
        return $user;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws EntityNotFoundException
     * @throws \App\Exceptions\EntityException
     */
    public function update(int $id, ArrayHash $data, bool $flush = true): ?BaseEntity
    {
        $user = $this->repository->find($id);
        if(!$user){
            throw new EntityNotFoundException('Entity for update not found.');
        }
        $user->setUsername($data->username);
        $user->setFirstName($data->firstName);
        $user->setLastName($data->lastName);
        if($data->changePassword){
            $user->setPassword($data->password);
        }
        $user->setRole($this->roleRepository->find($data->role));
        $user->setGroups(new ArrayCollection());
        $user = $this->attachGroups($user, $data->groups);
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
        foreach ($groups as $group){
            $user->addGroup($this->groupRepository->find($group));
        }
        return $user;
    }
}
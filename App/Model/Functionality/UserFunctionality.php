<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.5.19
 * Time: 22:10
 */

namespace App\Model\Functionality;

use App\Model\Entity\User;
use App\Model\Manager\ConstraintEntityManager;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\RoleRepository;
use App\Model\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
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
     * @return Object|null
     * @throws \Exception
     */
    public function create(ArrayHash $data): ?Object
    {
        $user = new User();
        $user->setUsername($data->username);
        $user->setPassword(Passwords::hash($data->password));
        $user->setRole($this->roleRepository->find($data->role));
        $user = $this->attachGroups($user, $data->groups);
        if(isset($data->created)){
            $user->setCreated($data->created);
        }
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @return Object
     * @throws \Exception
     */
    public function update(int $id, ArrayHash $data): ?Object
    {
        $user = $this->repository->find($id);
        if(!$user){
            throw new EntityNotFoundException('Entity for update not found.');
        }
        $user->setUsername($data->username);
        if($data->change_password){
            $user->setPassword(Passwords::hash($data->password));
        }
        $user->setRole($this->roleRepository->find($data->role));
        $user->setGroups(new ArrayCollection());
        $user = $this->attachGroups($user, $data->groups);
        $this->em->persist($user);
        $this->em->flush();
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
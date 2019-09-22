<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 23:13
 */

namespace App\Model\Persistent\Functionality;

use App\Model\Persistent\Entity\BaseEntity;
use App\Model\Persistent\Entity\Group;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\CategoryRepository;
use App\Model\Persistent\Repository\GroupRepository;
use App\Model\Persistent\Repository\SuperGroupRepository;
use App\Model\Persistent\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Utils\ArrayHash;

/**
 * Class GroupFunctionality
 * @package App\Model\Persistent\Functionality
 */
class GroupFunctionality extends BaseFunctionality
{
    /**
     * @var SuperGroupRepository
     */
    protected $superGroupRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * GroupFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param GroupRepository $repository
     * @param SuperGroupRepository $superGroupRepository
     * @param CategoryRepository $categoryRepository
     * @param UserRepository $userRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager, GroupRepository $repository,
        SuperGroupRepository $superGroupRepository, CategoryRepository $categoryRepository,
        UserRepository $userRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $repository;
        $this->superGroupRepository = $superGroupRepository;
        $this->categoryRepository = $categoryRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param ArrayHash $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\Exceptions\EntityException
     */
    public function create(ArrayHash $data, bool $flush = true): ?BaseEntity
    {
        $group = new Group();
        $group->setLabel($data->label);
        $group->setSuperGroup($this->superGroupRepository->find($data->superGroup));
        if(isset($data->user_id)){
            $group->setCreatedBy($this->userRepository->find($data->user_id));
        }
        if(isset($data->created)){
            $group->setCreated($data->created);
        }
        $this->em->persist($group);
        if($flush){
            $this->em->flush();
        }
        return $group;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\Exceptions\EntityException
     */
    public function update(int $id, ArrayHash $data, bool $flush = true): ?BaseEntity
    {
        $group = $this->repository->find($id);
        if(isset($data->label)){
            $group->setLabel($data->label);
        }
        if(isset($data->superGroup)){
            $group->setSuperGroup($this->superGroupRepository->find($data->superGroup));
        }
        $this->em->persist($group);
        if($flush){
            $this->em->flush();
        }
        return $group;
    }

    /**
     * @param int $id
     * @param array $categories
     * @param bool $flush
     * @throws \App\Exceptions\EntityException
     */
    public function updatePermissions(int $id, array $categories, bool $flush = true): void
    {
        $group = $this->repository->find($id);
        $group->setCategories(new ArrayCollection());
        $group = $this->attachCategories($group, $categories);
        $this->em->persist($group);
        if($flush){
            $this->em->flush();
        }
    }

    /**
     * @param Group $group
     * @param array $categories
     * @return Group
     */
    protected function attachCategories(Group $group, array $categories): Group
    {
        foreach ($categories as $category){
            $group->addCategory($this->categoryRepository->find($category));
        }
        return $group;
    }
}
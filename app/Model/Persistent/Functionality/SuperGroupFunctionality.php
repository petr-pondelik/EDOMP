<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 22:14
 */

namespace App\Model\Persistent\Functionality;

use App\Model\Persistent\Entity\BaseEntity;
use App\Model\Persistent\Entity\SuperGroup;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\CategoryRepository;
use App\Model\Persistent\Repository\SuperGroupRepository;
use App\Model\Persistent\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Utils\ArrayHash;

/**
 * Class SuperGroupFunctionality
 * @package App\Model\Persistent\Functionality
 */
class SuperGroupFunctionality extends BaseFunctionality
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var GroupFunctionality
     */
    protected $groupFunctionality;

    /**
     * SuperGroupFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param SuperGroupRepository $repository
     * @param CategoryRepository $categoryRepository
     * @param UserRepository $userRepository
     * @param GroupFunctionality $groupFunctionality
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager, SuperGroupRepository $repository,
        CategoryRepository $categoryRepository, UserRepository $userRepository,
        GroupFunctionality $groupFunctionality
    )
    {
        parent::__construct($entityManager);
        $this->repository = $repository;
        $this->categoryRepository = $categoryRepository;
        $this->userRepository = $userRepository;
        $this->groupFunctionality = $groupFunctionality;
    }

    /**
     * @param ArrayHash $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\Exceptions\EntityException
     */
    public function create(ArrayHash $data, bool $flush = true): ?BaseEntity
    {
        $superGroup = new SuperGroup();
        $superGroup->setLabel($data->label);
        if(isset($data->user_id)){
            $superGroup->setCreatedBy($this->userRepository->find($data->user_id));
        }
        $this->em->persist($superGroup);
        if ($flush) {
            $this->em->flush();
        }
        return $superGroup;
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
        $superGroup = $this->repository->find($id);
        $superGroup->setLabel($data->label);
        $this->em->persist($superGroup);
        if ($flush) {
            $this->em->flush();
        }
        return $superGroup;
    }

    /**
     * @param int $id
     * @param $categories
     * @param bool $flush
     * @throws \App\Exceptions\EntityException
     */
    public function updatePermissions(int $id, $categories, bool $flush = true): void
    {
        $superGroup = $this->repository->find($id);
        $superGroup->setCategories(new ArrayCollection());
        $superGroup = $this->attachCategories($superGroup, $categories);
        foreach ($superGroup->getGroups()->getValues() as $group){
            $this->groupFunctionality->updatePermissions($group->getId(), $categories);
        }
        $this->em->persist($superGroup);
        if ($flush) {
            $this->em->flush();
        }
    }

    /**
     * @param SuperGroup $superGroup
     * @param array $categories
     * @return SuperGroup
     */
    protected function attachCategories(SuperGroup $superGroup, array $categories): SuperGroup
    {
        foreach ($categories as $category){
            $superGroup->addCategory($this->categoryRepository->find($category));
        }
        return $superGroup;
    }
}
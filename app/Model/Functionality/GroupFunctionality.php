<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 23:13
 */

namespace App\Model\Functionality;

use App\Model\Entity\Group;
use App\Model\Manager\ConstraintEntityManager;
use App\Model\Repository\CategoryRepository;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\SuperGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Utils\ArrayHash;

/**
 * Class GroupFunctionality
 * @package App\Model\Functionality
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
     * GroupFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param GroupRepository $repository
     * @param SuperGroupRepository $superGroupRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager, GroupRepository $repository,
        SuperGroupRepository $superGroupRepository, CategoryRepository $categoryRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $repository;
        $this->superGroupRepository = $superGroupRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param ArrayHash $data
     * @return Object|null
     * @throws \Exception
     */
    public function create(ArrayHash $data): ?Object
    {
        $group = new Group();
        $group->setLabel($data->label);
        $group->setSuperGroup($this->superGroupRepository->find($data->super_group_id));
        if(isset($data->created)){
            $group->setCreated($data->created);
        }
        $this->em->persist($group);
        $this->em->flush();
        return $group;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @return Object
     * @throws \Exception
     */
    public function update(int $id, ArrayHash $data): ?Object
    {
        $group = $this->repository->find($id);
        if(isset($data->label)){
            $group->setLabel($data->label);
        }
        if(isset($data->super_group_id)){
            $group->setSuperGroup($this->superGroupRepository->find($data->super_group_id));
        }
        $this->em->persist($group);
        $this->em->flush();
        return $group;
    }

    /**
     * @param int $id
     * @param array $categories
     * @throws \Exception
     */
    public function updatePermissions(int $id, array $categories): void
    {
        $group = $this->repository->find($id);
        $group->setCategories(new ArrayCollection());
        $group = $this->attachCategories($group, $categories);
        $this->em->persist($group);
        $this->em->flush();
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
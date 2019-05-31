<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 22:14
 */

namespace App\Model\Functionality;

use App\Model\Entity\SuperGroup;
use App\Model\Manager\ConstraintEntityManager;
use App\Model\Repository\CategoryRepository;
use App\Model\Repository\SuperGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Utils\ArrayHash;

/**
 * Class SuperGroupFunctionality
 * @package App\Model\Functionality
 */
class SuperGroupFunctionality extends BaseFunctionality
{

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var GroupFunctionality
     */
    protected $groupFunctionality;

    /**
     * SuperGroupFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param SuperGroupRepository $repository
     * @param CategoryRepository $categoryRepository
     * @param GroupFunctionality $groupFunctionality
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager, SuperGroupRepository $repository,
        CategoryRepository $categoryRepository,
        GroupFunctionality $groupFunctionality
    )
    {
        parent::__construct($entityManager);
        $this->repository = $repository;
        $this->categoryRepository = $categoryRepository;
        $this->groupFunctionality = $groupFunctionality;
    }

    /**
     * @param ArrayHash $data
     * @return Object|null
     * @throws \Exception
     */
    public function create(ArrayHash $data): ?Object
    {
        $superGroup = new SuperGroup();
        $superGroup->setLabel($data->label);
        $this->em->persist($superGroup);
        $this->em->flush();
        return $superGroup;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @return Object
     * @throws \Exception
     */
    public function update(int $id, ArrayHash $data): ?Object
    {
        $superGroup = $this->repository->find($id);
        $superGroup->setLabel($data->label);
        $this->em->persist($superGroup);
        $this->em->flush();
        return $superGroup;
    }

    /**
     * @param int $id
     * @param $categories
     * @throws \Exception
     */
    public function updatePermissions(int $id, $categories): void
    {
        $superGroup = $this->repository->find($id);
        $superGroup->setCategories(new ArrayCollection());
        $superGroup = $this->attachCategories($superGroup, $categories);
        foreach ($superGroup->getGroups()->getValues() as $group)
            $this->groupFunctionality->updatePermissions($group->getId(), $categories);
        $this->em->persist($superGroup);
        $this->em->flush();
    }

    /**
     * @param SuperGroup $superGroup
     * @param array $categories
     * @return SuperGroup
     */
    public function attachCategories(SuperGroup $superGroup, array $categories): SuperGroup
    {
        foreach ($categories as $category)
            $superGroup->addCategory($this->categoryRepository->find($category));
        return $superGroup;
    }
}
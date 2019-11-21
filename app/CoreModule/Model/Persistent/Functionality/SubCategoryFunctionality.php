<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 23:30
 */

namespace App\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Entity\SubCategory;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\CategoryRepository;
use App\CoreModule\Model\Persistent\Repository\SubCategoryRepository;
use App\CoreModule\Model\Persistent\Repository\UserRepository;

/**
 * Class SubCategoryFunctionality
 * @package App\CoreModule\Model\Persistent\Functionality
 */
class SubCategoryFunctionality extends BaseFunctionality
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
     * SubCategoryFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param SubCategoryRepository $subCategoryRepository
     * @param CategoryRepository $categoryRepository
     * @param UserRepository $userRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager,
        SubCategoryRepository $subCategoryRepository,
        CategoryRepository $categoryRepository,
        UserRepository $userRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $subCategoryRepository;
        $this->categoryRepository = $categoryRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function create(iterable $data, bool $flush = true): ?BaseEntity
    {
        $subcategory = new SubCategory();
        $category = $this->categoryRepository->find($data->category);
        $subcategory->setLabel($data->label);
        $subcategory->setCategory($category);
        $subcategory->setCreatedBy($this->userRepository->find($data->userId));
        $this->em->persist($subcategory);
        if ($flush) {
            $this->em->flush();
        }
        return $subcategory;
    }

    /**
     * @param int $id
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function update(int $id, iterable $data, bool $flush = true): ?BaseEntity
    {
        $subcategory = $this->repository->find($id);
        if(!empty($data->label)){
            $subcategory->setLabel($data->label);
        }
        if(!empty($data->category)) {
            $category = $this->categoryRepository->find($data->category);
            $subcategory->setCategory($category);
        }
        $this->em->persist($subcategory);
        if ($flush) {
            $this->em->flush();
        }
        return $subcategory;
    }
}
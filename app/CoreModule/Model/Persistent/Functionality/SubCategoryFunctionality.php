<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 23:30
 */

namespace App\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Entity\SubCategory;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\CategoryRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;

/**
 * Class SubCategoryFunctionality
 * @package App\Model\Persistent\Functionality
 */
class SubCategoryFunctionality extends BaseFunctionality
{

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * SubCategoryFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param SubCategoryRepository $subCategoryRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager,
        SubCategoryRepository $subCategoryRepository, CategoryRepository $categoryRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $subCategoryRepository;
        $this->categoryRepository = $categoryRepository;
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
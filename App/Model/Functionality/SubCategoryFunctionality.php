<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 23:30
 */

namespace App\Model\Functionality;

use App\Model\Entity\SubCategory;
use App\Model\Manager\ConstraintEntityManager;
use App\Model\Repository\CategoryRepository;
use App\Model\Repository\SubCategoryRepository;
use Nette\Utils\ArrayHash;

/**
 * Class SubCategoryFunctionality
 * @package App\Model\Functionality
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
     * @param ArrayHash $data
     * @return Object|null
     * @throws \Exception
     */
    public function create(ArrayHash $data): ?Object
    {
        $subcategory = new SubCategory();
        $category = $this->categoryRepository->find($data->category_id);
        $subcategory->setLabel($data->label);
        $subcategory->setCategory($category);
        $this->em->persist($subcategory);
        $this->em->flush();
        return $subcategory;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @return Object
     * @throws \Exception
     */
    public function update(int $id, ArrayHash $data): ?Object
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
        $this->em->flush();
        return $subcategory;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 23:30
 */

namespace App\Model\Functionality;

use App\Model\Entity\SubCategory;
use App\Model\Repository\CategoryRepository;
use App\Model\Repository\SubCategoryRepository;
use Kdyby\Doctrine\EntityManager;
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
     * @param EntityManager $entityManager
     * @param SubCategoryRepository $subCategoryRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct
    (
        EntityManager $entityManager,
        SubCategoryRepository $subCategoryRepository, CategoryRepository $categoryRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $subCategoryRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param ArrayHash $data
     * @return void
     * @throws \Exception
     */
    public function create(ArrayHash $data): void
    {
        $subcategory = new SubCategory();
        $category = $this->categoryRepository->find($data->category);

        $subcategory->setLabel($data->label);
        $subcategory->setCategory($category);

        $this->em->persist($subcategory);
        $this->em->flush();
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @throws \Exception
     */
    public function update(int $id, ArrayHash $data): void
    {
        $subcategory = $this->repository->find($id);

        if(!empty($data->label))
            $subcategory->setLabel($data->label);

        if(!empty($data->category)) {
            $category = $this->categoryRepository->find($data->category);
            $subcategory->setCategory($category);
        }

        $this->em->persist($subcategory);
        $this->em->flush();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 16:13
 */

namespace App\Model\Functionality;

use App\Model\Entity\Category;
use App\Model\Repository\CategoryRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\ArrayHash;

/**
 * Class CategoryFunctionality
 * @package App\Model\Functionality
 */
class CategoryFunctionality extends BaseFunctionality
{
    /**
     * CategoryFunctionality constructor.
     * @param EntityManager $entityManager
     * @param CategoryRepository $categoryRepository
     */
    public function __construct
    (
        EntityManager $entityManager,
        CategoryRepository $categoryRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $categoryRepository;
    }

    /**
     * @param ArrayHash $data
     * @return int
     * @throws \Exception
     */
    public function create(ArrayHash $data): int
    {
        $category = new Category();
        $category->setLabel($data->label);
        $this->em->persist($category);
        $this->em->flush();
        return $category->getId();
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @return Object|null
     * @throws \Exception
     */
    public function update(int $id, ArrayHash $data): ?Object
    {
        $category = $this->repository->find($id);
        $category->setLabel($data->label);
        $this->em->persist($category);
        $this->em->flush();
        return $category;
    }
}
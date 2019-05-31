<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 16:13
 */

declare(strict_types=1);

namespace App\Model\Functionality;

use App\Model\Entity\Category;
use App\Model\Manager\ConstraintEntityManager;
use App\Model\Repository\CategoryRepository;
use Nette\Utils\ArrayHash;

/**
 * Class CategoryFunctionality
 * @package App\Model\Functionality
 */
final class CategoryFunctionality extends BaseFunctionality
{
    /**
     * CategoryFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param CategoryRepository $categoryRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager,
        CategoryRepository $categoryRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $categoryRepository;
    }

    /**
     * @param ArrayHash $data
     * @return Object|null
     * @throws \Exception
     */
    public function create(ArrayHash $data): ?Object
    {
        $category = new Category();
        $category->setLabel($data->label);
        $this->em->persist($category);
        $this->em->flush();
        return $category;
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

    public function getTest(): int
    {
        return 1;
    }
}
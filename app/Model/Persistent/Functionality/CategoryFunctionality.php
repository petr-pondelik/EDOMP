<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 16:13
 */

declare(strict_types=1);

namespace App\Model\Persistent\Functionality;

use App\Model\Persistent\Entity\BaseEntity;
use App\Model\Persistent\Entity\Category;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\CategoryRepository;
use Nette\Utils\ArrayHash;

/**
 * Class CategoryFunctionality
 * @package App\Model\Persistent\Functionality
 */
class CategoryFunctionality extends BaseFunctionality
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
     * @return BaseEntity|null
     * @throws \App\Exceptions\EntityException
     */
    public function create(ArrayHash $data): ?BaseEntity
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
     * @return BaseEntity|null
     * @throws \App\Exceptions\EntityException
     */
    public function update(int $id, ArrayHash $data): ?BaseEntity
    {
        $category = $this->repository->find($id);
        $category->setLabel($data->label);
        $this->em->persist($category);
        $this->em->flush();
        return $category;
    }
}
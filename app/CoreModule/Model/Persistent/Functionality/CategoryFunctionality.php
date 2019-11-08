<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 16:13
 */

declare(strict_types=1);

namespace App\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Entity\Category;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\CategoryRepository;

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
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function create(iterable $data, bool $flush = true): ?BaseEntity
    {
        $category = new Category();
        $category->setLabel($data->label);
        $this->em->persist($category);
        if($flush){
            $this->em->flush();
        }
        return $category;
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
        $category = $this->repository->find($id);
        $category->setLabel($data->label);
        $this->em->persist($category);
        if($flush){
            $this->em->flush();
        }
        return $category;
    }
}
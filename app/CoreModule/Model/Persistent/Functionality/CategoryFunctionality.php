<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 16:13
 */

declare(strict_types=1);

namespace App\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Entity\Category;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\CategoryRepository;
use App\CoreModule\Model\Persistent\Repository\UserRepository;

/**
 * Class CategoryFunctionality
 * @package App\CoreModule\Model\Persistent\Functionality
 */
class CategoryFunctionality extends BaseFunctionality
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * CategoryFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param CategoryRepository $categoryRepository
     * @param UserRepository $userRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager,
        CategoryRepository $categoryRepository,
        UserRepository $userRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $categoryRepository;
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
        $category = new Category();
        $category->setLabel($data->label);
        $category->setCreatedBy($this->userRepository->find($data->userId));
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
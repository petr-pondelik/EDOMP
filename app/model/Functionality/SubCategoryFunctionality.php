<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 23:30
 */

namespace App\Model\Functionality;

use App\Model\Entity\SubCategory;
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
     * SubCategoryFunctionality constructor.
     * @param EntityManager $entityManager
     * @param SubCategoryRepository $subCategoryRepository
     */
    public function __construct
    (
        EntityManager $entityManager,
        SubCategoryRepository $subCategoryRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $subCategoryRepository;
    }

    /**
     * @param ArrayHash $data
     * @return void
     * @throws \Exception
     */
    public function create(ArrayHash $data): void
    {
        $subcategory = new SubCategory();
        $subcategory->setLabel($data->label);
        $subcategory->setCategory($data->category);
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
        if(!empty($data->category))
            $subcategory->setCategory($data->category);
        $this->em->persist($subcategory);
        $this->em->flush();
    }
}
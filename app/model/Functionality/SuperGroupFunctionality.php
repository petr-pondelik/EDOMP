<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 22:14
 */

namespace App\Model\Functionality;

use App\Model\Entity\SuperGroup;
use App\Model\Repository\SuperGroupRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\ArrayHash;

/**
 * Class SuperGroupFunctionality
 * @package App\Model\Functionality
 */
class SuperGroupFunctionality extends BaseFunctionality
{

    /**
     * SuperGroupFunctionality constructor.
     * @param EntityManager $entityManager
     * @param SuperGroupRepository $repository
     */
    public function __construct(EntityManager $entityManager, SuperGroupRepository $repository)
    {
        parent::__construct($entityManager);
        $this->repository = $repository;
    }

    /**
     * @param ArrayHash $data
     * @return int
     * @throws \Exception
     */
    public function create(ArrayHash $data): int
    {
        $superGroup = new SuperGroup();
        $superGroup->setLabel($data->label);
        $this->em->persist($superGroup);
        $this->em->flush();
        return $superGroup->getId();
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @return Object
     * @throws \Exception
     */
    public function update(int $id, ArrayHash $data): ?Object
    {
        $superGroup = $this->repository->find($id);
        $superGroup->setLabel($data->label);
        $this->em->persist($superGroup);
        $this->em->flush();
        return $superGroup;
    }
}
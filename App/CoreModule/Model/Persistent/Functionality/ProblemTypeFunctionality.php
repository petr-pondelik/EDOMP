<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 31.5.19
 * Time: 21:15
 */

namespace App\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Entity\ProblemType;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use Doctrine\ORM\EntityNotFoundException;

/**
 * Class ProblemTypeFunctionality
 * @package App\CoreModule\Model\Persistent\Functionality
 */
class ProblemTypeFunctionality extends BaseFunctionality
{
    /**
     * ProblemTypeFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param ProblemTypeRepository $problemTypeRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager,
        ProblemTypeRepository $problemTypeRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $problemTypeRepository;
    }

    /**
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function create(iterable $data, bool $flush = true): ?BaseEntity
    {
        $problemType = new ProblemType();
        $problemType->setLabel($data->label);
        $this->em->persist($problemType);
        if ($flush) {
            $this->em->flush();
        }
        return $problemType;
    }

    /**
     * @param int $id
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws EntityNotFoundException
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function update(int $id, iterable $data, bool $flush = true): ?BaseEntity
    {
        $problemType = $this->repository->find($id);
        if(!$problemType){
            throw new EntityNotFoundException('Entity for update not found.');
        }
        $problemType->setLabel($data->label);
        $this->em->persist($problemType);
        if ($flush) {
            $this->em->flush();
        }
        return $problemType;
    }
}
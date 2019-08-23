<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 31.5.19
 * Time: 21:15
 */

namespace App\Model\Persistent\Functionality;

use App\Model\Persistent\Entity\ProblemType;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemTypeFunctionality
 * @package App\Model\Persistent\Functionality
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
     * @param ArrayHash $data
     * @return Object|null
     * @throws \Exception
     */
    public function create(ArrayHash $data): ?Object
    {
        $problemType = new ProblemType();
        $problemType->setLabel($data->label);
        $this->em->persist($problemType);
        $this->em->flush();
        return $problemType;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @return Object|null
     * @throws \App\Exceptions\EntityException
     * @throws EntityNotFoundException
     */
    public function update(int $id, ArrayHash $data): ?Object
    {
        $problemType = $this->repository->find($id);
        if(!$problemType){
            throw new EntityNotFoundException('Entity for update not found.');
        }
        $problemType->setLabel($data->label);
        $this->em->persist($problemType);
        $this->em->flush();
        return $problemType;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 11.9.19
 * Time: 20:58
 */

namespace App\Model\Persistent\Functionality\ProblemFinal;

use App\Model\Persistent\Entity\BaseEntity;
use App\Model\Persistent\Entity\ProblemFinal\GeometricSequenceFinal;
use App\Model\Persistent\Functionality\BaseFunctionality;
use App\Model\Persistent\Traits\ProblemFinalFunctionalityTrait;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;

/**
 * Class GeometricSequenceFinalFunctionality
 * @package App\Model\Persistent\Functionality\ProblemFinal
 */
class GeometricSequenceFinalFunctionality extends BaseFunctionality
{
    use ProblemFinalFunctionalityTrait;

    /**
     * @param ArrayHash $data
     * @param array|null $conditions
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\Exceptions\EntityException
     */
    public function create(ArrayHash $data, array $conditions = null, bool $flush = true): ?BaseEntity
    {
        $entity = new GeometricSequenceFinal();
        $entity = $this->setBasics($entity, $data);
        $entity->setIndexVariable($data->indexVariable);
        $entity->setFirstN($data->firstN);

        if($conditions === null){
            $entity = $this->attachConditions($entity, $data);
        }
        else{
            $entity->setConditions($conditions);
        }

        $this->em->persist($entity);
        if($flush){
            $this->em->flush();
        }

        return $entity;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @param array|null $conditions
     * @param bool $flush
     * @return BaseEntity|null
     * @throws EntityNotFoundException
     * @throws \App\Exceptions\EntityException
     */
    public function update(int $id, ArrayHash $data, array $conditions = null, bool $flush = true): ?BaseEntity
    {
        $entity = $this->repository->find($id);
        if(!$entity){
            throw new EntityNotFoundException('Entity for update not found.');
        }

        $this->setBasics($entity, $data);
        $entity->setIndexVariable($data->indexVariable);
        $entity->setFirstN($data->firstN);

        if($conditions === null){
            $entity = $this->attachConditions($entity, $data);
        }
        else{
            $entity->setConditions($conditions);
        }

        $this->em->persist($entity);
        if($flush){
            $this->em->flush();
        }

        return $entity;
    }
}
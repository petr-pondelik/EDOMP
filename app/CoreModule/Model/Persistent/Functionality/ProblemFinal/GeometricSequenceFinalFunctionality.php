<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 11.9.19
 * Time: 20:58
 */

namespace App\Model\Persistent\Functionality\ProblemFinal;

use App\CoreModule\Helpers\FormatterHelper;
use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal\GeometricSequenceFinal;
use App\Model\Persistent\Functionality\BaseFunctionality;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\ProblemConditionRepository;
use App\Model\Persistent\Repository\ProblemFinal\GeometricSequenceFinalRepository;
use App\Model\Persistent\Repository\ProblemRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Model\Persistent\Traits\ProblemFinalFunctionalityTrait;
use Doctrine\ORM\EntityNotFoundException;

/**
 * Class GeometricSequenceFinalFunctionality
 * @package App\Model\Persistent\Functionality\ProblemFinal
 */
class GeometricSequenceFinalFunctionality extends BaseFunctionality
{
    use ProblemFinalFunctionalityTrait;

    /**
     * ArithmeticSequenceFinalFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param GeometricSequenceFinalRepository $repository
     * @param ProblemRepository $problemRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param DifficultyRepository $difficultyRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param FormatterHelper $formatterHelper
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager,
        GeometricSequenceFinalRepository $repository,
        ProblemRepository $problemRepository,
        ProblemTypeRepository $problemTypeRepository, ProblemConditionRepository $problemConditionRepository,
        DifficultyRepository $difficultyRepository, SubCategoryRepository $subCategoryRepository,
        FormatterHelper $formatterHelper
    )
    {
        parent::__construct($entityManager);
        $this->repository = $repository;
        $this->injectRepositories($problemTypeRepository, $problemConditionRepository, $difficultyRepository, $subCategoryRepository, $problemRepository, $formatterHelper);
    }

    /**
     * @param iterable $data
     * @param bool $flush
     * @param array|null $conditions
     * @return BaseEntity|null
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function create(iterable $data, bool $flush = true, array $conditions = null): ?BaseEntity
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
     * @param iterable $data
     * @param bool $flush
     * @param array|null $conditions
     * @return BaseEntity|null
     * @throws EntityNotFoundException
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function update(int $id, iterable $data, bool $flush = true, array $conditions = null): ?BaseEntity
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
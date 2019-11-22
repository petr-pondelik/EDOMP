<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 11.9.19
 * Time: 20:58
 */

namespace App\CoreModule\Model\Persistent\Functionality\ProblemFinal;

use App\CoreModule\Helpers\FormatterHelper;
use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal\GeometricSequenceFinal;
use App\CoreModule\Model\Persistent\Functionality\BaseFunctionality;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemFinal\GeometricSequenceFinalRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;
use App\CoreModule\Model\Persistent\Repository\UserRepository;
use App\CoreModule\Model\Persistent\Traits\ProblemFinalFunctionalityTrait;
use Doctrine\ORM\EntityNotFoundException;

/**
 * Class GeometricSequenceFinalFunctionality
 * @package App\CoreModule\Model\Persistent\Functionality\ProblemFinal
 */
class GeometricSequenceFinalFunctionality extends BaseFunctionality
{
    use ProblemFinalFunctionalityTrait;

    /**
     * ArithmeticSequenceFinalFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param GeometricSequenceFinalRepository $repository
     * @param ProblemRepository $problemRepository
     * @param UserRepository $userRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param DifficultyRepository $difficultyRepository
     * @param SubThemeRepository $subThemeRepository
     * @param FormatterHelper $formatterHelper
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager,
        GeometricSequenceFinalRepository $repository,
        ProblemRepository $problemRepository,
        UserRepository $userRepository,
        ProblemTypeRepository $problemTypeRepository,
        ProblemConditionRepository $problemConditionRepository,
        DifficultyRepository $difficultyRepository,
        SubThemeRepository $subThemeRepository,
        FormatterHelper $formatterHelper
    )
    {
        parent::__construct($entityManager);
        $this->repository = $repository;
        $this->injectRepositories($problemTypeRepository, $problemConditionRepository, $difficultyRepository, $subThemeRepository, $problemRepository, $userRepository, $formatterHelper);
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
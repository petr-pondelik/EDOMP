<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 11.9.19
 * Time: 20:57
 */

namespace App\Model\Persistent\Functionality\ProblemFinal;

use App\Helpers\FormatterHelper;
use App\Model\Persistent\Entity\BaseEntity;
use App\Model\Persistent\Entity\ProblemFinal\QuadraticEquationFinal;
use App\Model\Persistent\Functionality\BaseFunctionality;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\ProblemConditionRepository;
use App\Model\Persistent\Repository\ProblemFinal\QuadraticEquationFinalRepository;
use App\Model\Persistent\Repository\ProblemRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Model\Persistent\Traits\ProblemFinalFunctionalityTrait;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;

/**
 * Class QuadraticEquationFinalFunctionality
 * @package App\Model\Persistent\Functionality\ProblemFinal
 */
class QuadraticEquationFinalFunctionality extends BaseFunctionality
{
    use ProblemFinalFunctionalityTrait;

    /**
     * LinearEquationFinalFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param QuadraticEquationFinalRepository $repository
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
        QuadraticEquationFinalRepository $repository,
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
     * @param ArrayHash $data
     * @param array|null $conditions
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\Exceptions\EntityException
     */
    public function create(ArrayHash $data, array $conditions = null, bool $flush = true): ?BaseEntity
    {
        $entity = new QuadraticEquationFinal();
        $entity = $this->setBasics($entity, $data);
        $entity->setVariable($data->variable);

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
        $entity->setVariable($data->variable);

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
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 16:52
 */

namespace App\Model\Persistent\Functionality\ProblemFinal;

use App\Helpers\FormatterHelper;
use App\Model\Persistent\Entity\BaseEntity;
use App\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
use App\Model\Persistent\Functionality\BaseFunctionality;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\ProblemConditionRepository;
use App\Model\Persistent\Repository\ProblemFinal\ProblemFinalRepository;
use App\Model\Persistent\Repository\ProblemRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Model\Persistent\Traits\ProblemFinalFunctionalityTrait;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemFinalFunctionality
 * @package App\Model\Persistent\Functionality
 */
class ProblemFinalFunctionality extends BaseFunctionality
{
    use ProblemFinalFunctionalityTrait;

    /**
     * ProblemFinalFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param ProblemFinalRepository $repository
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
        ProblemFinalRepository $repository,
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
     * @return Object|null
     * @throws \App\Exceptions\EntityException
     * @throws \Exception
     */
    public function create(ArrayHash $data): ?BaseEntity
    {
        $problemFinal = new ProblemFinal();
        $problemFinal = $this->setBasics($problemFinal, $data);
        $this->em->persist($problemFinal);
        $this->em->flush();
        return $problemFinal;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @return BaseEntity|null
     * @throws EntityNotFoundException
     * @throws \App\Exceptions\EntityException
     */
    public function update(int $id, ArrayHash $data): ?BaseEntity
    {
        $problemFinal = $this->repository->find($id);
        if(!$problemFinal){
            throw new EntityNotFoundException('Entity for update not found.');
        }

        $this->setBasics($problemFinal, $data);

//        if($updateConditions && isset($data->problemType)){
//            $problem->setConditions(new ArrayCollection());
//            $this->attachConditions($problem, $data);
//        }

        $this->em->persist($problemFinal);
        $this->em->flush();

        return $problemFinal;
    }
}
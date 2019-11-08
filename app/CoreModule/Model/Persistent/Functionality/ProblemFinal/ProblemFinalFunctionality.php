<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 16:52
 */

namespace App\Model\Persistent\Functionality\ProblemFinal;

use App\CoreModule\Helpers\FormatterHelper;
use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
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
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\CoreModule\Exceptions\EntityException
     */
    public function create(iterable $data, bool $flush = true): ?BaseEntity
    {
        $problemFinal = new ProblemFinal();
        $problemFinal = $this->setBasics($problemFinal, $data);
        $this->em->persist($problemFinal);
        if ($flush) {
            $this->em->flush();
        }
        return $problemFinal;
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
        $problemFinal = $this->repository->find($id);
        if(!$problemFinal){
            throw new EntityNotFoundException('Entity for update not found.');
        }
        $this->setBasics($problemFinal, $data);
        $this->em->persist($problemFinal);
        if ($flush) {
            $this->em->flush();
        }
        return $problemFinal;
    }
}
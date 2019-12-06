<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 16:52
 */

namespace App\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Helpers\FormatterHelper;
use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemFinalRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\ProblemTemplateRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;
use App\CoreModule\Model\Persistent\Repository\UserRepository;
use App\CoreModule\Model\Persistent\Traits\ProblemFinalFunctionalityTrait;
use Doctrine\ORM\EntityNotFoundException;

/**
 * Class ProblemFinalFunctionality
 * @package App\CoreModule\Model\Persistent\Functionality
 */
class ProblemFinalFunctionality extends BaseFunctionality
{
    use ProblemFinalFunctionalityTrait;

    /**
     * ProblemFinalFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param ProblemFinalRepository $repository
     * @param ProblemTemplateRepository $problemTemplateRepository
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
        ProblemFinalRepository $repository,
        ProblemTemplateRepository $problemTemplateRepository,
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
        $this->injectRepositories(
            $problemTypeRepository, $problemConditionRepository, $difficultyRepository, $subThemeRepository,
            $problemTemplateRepository, $userRepository, $formatterHelper
        );
    }

    /**
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws EntityNotFoundException
     */
    public function create(iterable $data, bool $flush = true): ?BaseEntity
    {
        $problemFinal = new ProblemFinal();
        $problemFinal = $this->setBasics($problemFinal, $data);
        $problemFinal = $this->attachConditions($problemFinal, $data);

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
        /** @var ProblemFinal $problemFinal */
        $problemFinal = $this->repository->find($id);
        if (!$problemFinal) {
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
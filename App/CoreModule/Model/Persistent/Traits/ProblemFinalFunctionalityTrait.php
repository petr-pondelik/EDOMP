<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 11.9.19
 * Time: 21:50
 */

namespace App\CoreModule\Model\Persistent\Traits;

use App\CoreModule\Helpers\FormatterHelper;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal;
use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;
use App\CoreModule\Model\Persistent\Repository\UserRepository;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;

/**
 * Trait ProblemFinalFunctionalityTrait
 * @package App\CoreModule\Model\Persistent\Traits
 */
trait ProblemFinalFunctionalityTrait
{
    use ProblemFunctionalityTrait;

    /**
     * @var ProblemRepository
     */
    protected $problemRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var FormatterHelper
     */
    protected $formatterHelper;

    /**
     * @param ProblemTypeRepository $problemTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param DifficultyRepository $difficultyRepository
     * @param SubThemeRepository $subThemeRepository
     * @param ProblemRepository $problemRepository
     * @param UserRepository $userRepository
     * @param FormatterHelper $formatterHelper
     */
    public function injectRepositories
    (
        ProblemTypeRepository $problemTypeRepository,
        ProblemConditionRepository $problemConditionRepository,
        DifficultyRepository $difficultyRepository,
        SubThemeRepository $subThemeRepository,
        ProblemRepository $problemRepository,
        UserRepository $userRepository,
        FormatterHelper $formatterHelper
    ): void
    {
        $this->problemTypeRepository = $problemTypeRepository;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->difficultyRepository = $difficultyRepository;
        $this->subThemeRepository = $subThemeRepository;
        $this->problemRepository = $problemRepository;
        $this->userRepository = $userRepository;
        $this->formatterHelper = $formatterHelper;
    }

    /**
     * @param ProblemFinal $entity
     * @param ArrayHash $data
     * @return ProblemFinal
     */
    public function setBasics(ProblemFinal $entity, ArrayHash $data): ProblemFinal
    {
        bdump('SET BASICS');
        bdump($data);

        if (isset($data->body)) {
            $entity->setBody($data->body);
        }

        if (isset($data->problemType)) {
            $entity->setProblemType($this->problemTypeRepository->find($data->problemType));
        }

        if (isset($data->difficulty)) {
            $entity->setDifficulty($this->difficultyRepository->find($data->difficulty));
        }

        if (isset($data->subTheme)) {
            $entity->setSubTheme($this->subThemeRepository->find($data->subTheme));
        }

        if (isset($data->result)) {
            $entity->setResult($data->result);
        }

        if (isset($data->textBefore)) {
            $entity->setTextBefore($data->textBefore);
        }

        if (isset($data->textAfter)) {
            $entity->setTextAfter($data->textAfter);
        }

        if (isset($data->problemTemplateId)) {
            $entity->setProblemTemplate($this->problemRepository->find($data->problemTemplateId));
        }

        if (isset($data->isGenerated)) {
            $entity->setIsGenerated($data->isGenerated);
        }

        if (isset($data->matchesIndex)) {
            $entity->setMatchesIndex($data->matchesIndex);
        }

        if (isset($data->studentVisible)) {
            bdump('SET STUDENT VISIBLE');
            $entity->setStudentVisible($data->studentVisible);
        }

        if (isset($data->userId)) {
            $entity->setCreatedBy($this->userRepository->find($data->userId));
        }

        return $entity;
    }

    /**
     * @param ProblemFinal $problem
     * @param ArrayHash $data
     * @return ProblemFinal
     */
    public function attachConditions(ProblemFinal $problem, ArrayHash $data): ProblemFinal
    {
        if (isset($data->problemType)) {
            $type = $this->problemTypeRepository->find($data->problemType);
            $problemCondTypes = $type->getConditionTypes()->getValues();

            foreach ($problemCondTypes as $problemCondType) {

                //Get ConditionType ID
                $condTypeId = $problemCondType->getId();

                //Get ConditionType value from created problem
                $condTypeVal = $data->{'condition_' . $condTypeId};

                $condition = $this->problemConditionRepository->findOneBy([
                    'problemConditionType.id' => $condTypeId,
                    'accessor' => $condTypeVal
                ]);

                $problem->addCondition($condition);

            }
        }

        return $problem;
    }

    /**
     * @param int $id
     * @param ArrayHash $result
     * @throws \Exception
     */
    public function storeResult(int $id, ArrayHash $result): void
    {
        $problem = $this->repository->find($id);
        if (!$problem) {
            throw new EntityNotFoundException('Entity for update not found.');
        }
        $formatted = $this->formatterHelper->formatResult($result);
        $problem->setResult($formatted);
        $this->em->persist($problem);
        $this->em->flush();
    }
}
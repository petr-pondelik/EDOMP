<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 11.9.19
 * Time: 21:50
 */

namespace App\CoreModule\Model\Persistent\Traits;

use App\CoreModule\Helpers\FormatterHelper;
use App\CoreModule\Model\Persistent\Entity\Difficulty;
use App\CoreModule\Model\Persistent\Entity\ProblemConditionType;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal;
use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use App\CoreModule\Model\Persistent\Entity\ProblemType;
use App\CoreModule\Model\Persistent\Entity\SubTheme;
use App\CoreModule\Model\Persistent\Entity\User;
use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\ProblemTemplateRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;
use App\CoreModule\Model\Persistent\Repository\UserRepository;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;

/**
 * Trait ProblemFinalFunctionalityTrait
 * @package App\CoreModule\Model\Persistent\Traits
 */
trait ProblemFinalFunctionalityTrait
{
    use ProblemFunctionalityTrait;

    /**
     * @var ProblemTemplateRepository
     */
    protected $problemTemplateRepository;

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
     * @param ProblemTemplateRepository $problemTemplateRepository
     * @param UserRepository $userRepository
     * @param FormatterHelper $formatterHelper
     */
    public function injectRepositories
    (
        ProblemTypeRepository $problemTypeRepository,
        ProblemConditionRepository $problemConditionRepository,
        DifficultyRepository $difficultyRepository,
        SubThemeRepository $subThemeRepository,
        ProblemTemplateRepository $problemTemplateRepository,
        UserRepository $userRepository,
        FormatterHelper $formatterHelper
    ): void
    {
        $this->problemTypeRepository = $problemTypeRepository;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->difficultyRepository = $difficultyRepository;
        $this->subThemeRepository = $subThemeRepository;
        $this->problemTemplateRepository = $problemTemplateRepository;
        $this->userRepository = $userRepository;
        $this->formatterHelper = $formatterHelper;
    }

    /**
     * @param ProblemFinal $entity
     * @param iterable $data
     * @return ProblemFinal
     * @throws EntityNotFoundException
     */
    public function setBasics(ProblemFinal $entity, iterable $data): ProblemFinal
    {
        if (isset($data['body'])) {
            $entity->setBody($data['body']);
        }

        if (isset($data['problemType'])) {
            /** @var ProblemType|null $problemType */
            $problemType = $this->problemTypeRepository->find($data['problemType']);
            if (!$problemType) {
                throw new EntityNotFoundException('ProblemType not found.');
            }
            $entity->setProblemType($problemType);
        }

        if (isset($data['difficulty'])) {
            /** @var Difficulty|null $difficulty */
            $difficulty = $this->difficultyRepository->find($data['difficulty']);
            if (!$difficulty) {
                throw new EntityNotFoundException('Difficulty not found.');
            }
            $entity->setDifficulty($difficulty);
        }

        if (isset($data['subTheme'])) {
            /** @var SubTheme|null $subTheme */
            $subTheme = $this->subThemeRepository->find($data['subTheme']);
            if (!$subTheme) {
                throw new EntityNotFoundException('SubTheme not found.');
            }
            $entity->setSubTheme($subTheme);
        }

        if (isset($data['result'])) {
            $entity->setResult($data['result']);
        }

        if (isset($data['textBefore'])) {
            $entity->setTextBefore($data['textBefore']);
        }

        if (isset($data['textAfter'])) {
            $entity->setTextAfter($data['textAfter']);
        }

        if (isset($data['problemTemplateId'])) {
            /** @var ProblemTemplate|null $problemTemplate */
            $problemTemplate = $this->problemTemplateRepository->find($data['problemTemplateId']);
            if (!$problemTemplate) {
                throw new EntityNotFoundException('ProblemTemplate not found.');
            }
            $entity->setProblemTemplate($problemTemplate);
        }

        if (isset($data['isGenerated'])) {
            $entity->setIsGenerated($data['isGenerated']);
        }

        if (isset($data['matchesIndex'])) {
            $entity->setMatchesIndex($data['matchesIndex']);
        }

        if (isset($data['studentVisible'])) {
            $entity->setStudentVisible($data['studentVisible']);
        }

        if (isset($data['userId'])) {
            /** @var User|null $user */
            $user = $this->userRepository->find($data['userId']);
            if (!$user) {
                throw new EntityNotFoundException('User not found.');
            }
            $entity->setCreatedBy($user);
        }

        if (isset($data['created'])) {
            $entity->setCreated(DateTime::from($data['created']));
        }

        return $entity;
    }

    /**
     * @param ProblemFinal $problem
     * @param iterable $data
     * @return ProblemFinal
     * @throws EntityNotFoundException
     */
    public function attachConditions(ProblemFinal $problem, iterable $data): ProblemFinal
    {
        bdump($data);
        if (isset($data['problemType'])) {
            /** @var ProblemType|null $type */
            $type = $this->problemTypeRepository->find($data['problemType']);
            if (!$type) {
                throw new EntityNotFoundException('ProblemType not found.');
            }

            $problemCondTypes = $type->getConditionTypes()->getValues();

            foreach ($problemCondTypes as $problemCondType) {
                /** @var ProblemConditionType $problemCondType */
                // Get ConditionType ID
                $condTypeId = $problemCondType->getId();

                // Get ConditionType value from created problem
                $condTypeVal = $data['condition_' . $condTypeId];

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
        /** @var ProblemFinal|null $problem */
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
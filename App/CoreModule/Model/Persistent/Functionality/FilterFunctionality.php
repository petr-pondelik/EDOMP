<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.9.19
 * Time: 17:30
 */

namespace App\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Entity\Filter;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\Strings;

/**
 * Class FilterFunctionality
 * @package App\CoreModule\Model\Persistent\Functionality
 */
class FilterFunctionality extends BaseFunctionality
{
    /**
     * @var ProblemTypeRepository
     */
    protected $problemTypeRepository;

    /**
     * @var DifficultyRepository
     */
    protected $difficultyRepository;

    /**
     * @var SubThemeRepository
     */
    protected $subThemeRepository;

    /**
     * @var ProblemConditionRepository
     */
    protected $problemConditionRepository;

    /**
     * FilterFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param ProblemTypeRepository $problemTypeRepository
     * @param DifficultyRepository $difficultyRepository
     * @param SubThemeRepository $subThemeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager,
        ProblemTypeRepository $problemTypeRepository,
        DifficultyRepository $difficultyRepository,
        SubThemeRepository $subThemeRepository,
        ProblemConditionRepository $problemConditionRepository
    )
    {
        parent::__construct($entityManager);
        $this->problemTypeRepository = $problemTypeRepository;
        $this->difficultyRepository = $difficultyRepository;
        $this->subThemeRepository = $subThemeRepository;
        $this->problemConditionRepository = $problemConditionRepository;
    }

    /**
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws EntityNotFoundException
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \ReflectionException
     */
    public function create(iterable $data, bool $flush = true): ?BaseEntity
    {
        bdump('CREATE FILTER FUNCTIONALITY');

        $entity = new Filter();
        $reflection = new \ReflectionClass(Filter::class);

        foreach ($reflection->getProperties() as $property) {
            $propertyName = $property->getName();
            if (isset($data[$propertyName])) {
                $entity->{'set' . Strings::firstUpper($propertyName)}($data[$propertyName]);
            }
        }

        $this->attachEntities($entity, $data);

        $this->em->persist($entity);

        if ($flush) {
            $this->em->flush();
        }

        return $entity;
    }

    /**
     * @param int $id
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     */
    public function update(int $id, iterable $data, bool $flush = true): ?BaseEntity
    {
        return null;
    }

    /**
     * @param Filter $entity
     * @param iterable $data
     * @return Filter
     * @throws EntityNotFoundException
     */
    public function attachEntities(Filter $entity, iterable $data): Filter
    {
        bdump('FILTER: ATTACH ENTITIES');

        $difficulties = $this->difficultyRepository->findAssoc([], 'id');
        $subThemes = $this->subThemeRepository->findAssoc([], 'id');
        $problemTypes = $this->problemTypeRepository->findAssoc([], 'id');
        $problemConditions = $this->problemConditionRepository->findAssocByTypeAndAccessor();

        $selectedFilters = $data['selectedFilters'];

        foreach ($selectedFilters['difficulty'] as $id) {
            if (!isset($difficulties[$id])) {
                throw new EntityNotFoundException('Difficulty not found.');
            }
            $entity->addDifficulty($difficulties[$id]);
        }

        foreach ($selectedFilters['conditionType'] as $typeId => $accessors) {
            if ($accessors) {
                foreach ($accessors as $accessor) {
                    if (!isset($problemConditions[$problemConditions[$typeId][$accessor]])) {
                        throw new EntityNotFoundException('ProblemConditionType not found.');
                    }
                    $entity->addProblemCondition($problemConditions[$typeId][$accessor]);
                }
            }
        }

        foreach ($selectedFilters['subTheme'] as $id) {
            if (!isset($subThemes[$id])) {
                throw new EntityNotFoundException('SubTheme not found.');
            }
            $entity->addSubTheme($subThemes[$id]);
        }

        foreach ($selectedFilters['problemType'] as $id) {
            if (!isset($problemTypes[$id])) {
                throw new EntityNotFoundException('ProblemType not found.');
            }
            $entity->addProblemType($problemTypes[$id]);
        }

        return $entity;
    }
}
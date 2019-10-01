<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.9.19
 * Time: 17:30
 */

namespace App\Model\Persistent\Functionality;

use App\Model\Persistent\Entity\BaseEntity;
use App\Model\Persistent\Entity\Filter;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\ProblemConditionRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use Nette\Utils\Strings;

/**
 * Class FilterFunctionality
 * @package App\Model\Persistent\Functionality
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
     * @var SubCategoryRepository
     */
    protected $subCategoryRepository;

    /**
     * @var ProblemConditionRepository
     */
    protected $problemConditionRepository;

    /**
     * FilterFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param ProblemTypeRepository $problemTypeRepository
     * @param DifficultyRepository $difficultyRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemConditionRepository $problemConditionRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager,
        ProblemTypeRepository $problemTypeRepository,
        DifficultyRepository $difficultyRepository,
        SubCategoryRepository $subCategoryRepository,
        ProblemConditionRepository $problemConditionRepository
    )
    {
        parent::__construct($entityManager);
        $this->problemTypeRepository = $problemTypeRepository;
        $this->difficultyRepository = $difficultyRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->problemConditionRepository = $problemConditionRepository;
    }

    /**
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\Exceptions\EntityException
     * @throws \ReflectionException
     */
    public function create(iterable $data, bool $flush = true): ?BaseEntity
    {
        bdump('CREATE FILTER FUNCTIONALITY');
        bdump($data);

        $entity = new Filter();
        $reflection = new \ReflectionClass(Filter::class);

        foreach ($reflection->getProperties() as $property) {
            $propertyName = $property->getName();
            if (isset($data[$propertyName])) {
                $entity->{'set' . Strings::firstUpper($propertyName)}($data[$propertyName]);
            }
        }

        $this->attachEntities($entity, $data);

        bdump($entity);

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
     * @throws \Exception
     */
    public function attachEntities(Filter $entity, iterable $data): Filter
    {
        bdump('FILTER: ATTACH ENTITIES');

        $difficulties = $this->difficultyRepository->findAssoc([], 'id');
        $subCategories = $this->subCategoryRepository->findAssoc([], 'id');
        $problemTypes = $this->problemTypeRepository->findAssoc([], 'id');
        $problemConditions = $this->problemConditionRepository->findAssocByTypeAndAccessor();

        $selectedFilters = $data['selectedFilters'];

        foreach ($selectedFilters['difficulty'] as $id) {
            $entity->addDifficulty($difficulties[$id]);
        }

        foreach ($selectedFilters['conditionType'] as $typeId => $accessors) {
            bdump($accessors);
            if ($accessors) {
                foreach ($accessors as $accessor) {
                    bdump($accessor);
                    $entity->addProblemCondition($problemConditions[$typeId][$accessor]);
                }
            }
        }

        foreach ($selectedFilters['subCategory'] as $id) {
            $entity->addSubCategory($subCategories[$id]);
        }

        foreach ($selectedFilters['problemType'] as $id) {
            $entity->addProblemType($problemTypes[$id]);
        }

        return $entity;
    }
}
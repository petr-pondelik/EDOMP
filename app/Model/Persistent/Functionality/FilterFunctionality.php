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
use App\Model\Persistent\Entity\ProblemCondition;
use App\Model\Persistent\Entity\SubCategory;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\ProblemConditionRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use Nette\Utils\ArrayHash;
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
     * @var SubCategory
     */
    protected $subCategoryRepository;

    /**
     * @var ProblemCondition
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
     * @param ArrayHash $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\Exceptions\EntityException
     * @throws \ReflectionException
     */
    public function create(ArrayHash $data, bool $flush = true): ?BaseEntity
    {
        bdump('CREATE FILTER FUNCTIONALITY');
        bdump($data);

        $entity = new Filter();
        $reflection = new \ReflectionClass(Filter::class);

        foreach ($reflection->getProperties() as $property) {
            $propertyName = $property->getName();
            if (isset($data->{$propertyName})) {
                $entity->{'set' . Strings::firstUpper($propertyName)}($data->{$propertyName});
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
     * @param ArrayHash $data
     * @param bool $flush
     * @return BaseEntity|null
     */
    public function update(int $id, ArrayHash $data, bool $flush = true): ?BaseEntity
    {
        return null;
    }

    /**
     * @param Filter $entity
     * @param ArrayHash $data
     * @throws \Exception
     */
    public function attachEntities(Filter $entity, ArrayHash $data): void
    {
        $difficulties = $this->difficultyRepository->findAssoc([], 'id');

        $filters = $data->selectedFilters;

        foreach ($filters->difficulty as $difficultyId) {
            $entity->addDifficulty($difficulties[$difficultyId]);
        }

        $selectedFiltersArr = (array) $data->selectedFilters;
        bdump($selectedFiltersArr);

        foreach ($selectedFiltersArr as $key => $item){
            if(Strings::match($key, '~conditionType\d~')){

            }
        }

        bdump(array_keys($selectedFiltersArr));

//        $conditionTypes = Strings::match(array_keys($data)
//
//        foreach ($filters->)
    }
}
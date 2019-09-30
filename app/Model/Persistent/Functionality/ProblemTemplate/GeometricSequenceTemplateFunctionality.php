<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 18:28
 */

namespace App\Model\Persistent\Functionality\ProblemTemplate;

use App\Model\Persistent\Entity\BaseEntity;
use App\Model\Persistent\Entity\ProblemTemplate\GeometricSequenceTemplate;
use App\Model\Persistent\Functionality\BaseFunctionality;
use App\Model\Persistent\Functionality\TemplateJsonDataFunctionality;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\ProblemTemplate\GeometricSequenceTemplateRepository;
use App\Model\Persistent\Repository\ProblemConditionRepository;
use App\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Model\Persistent\Repository\TemplateJsonDataRepository;
use App\Model\Persistent\Traits\ProblemTemplateFunctionalityTrait;

/**
 * Class GeometricSequenceTemplateFunctionality
 * @package App\Model\Persistent\Functionality
 */
class GeometricSequenceTemplateFunctionality extends BaseFunctionality
{
    use ProblemTemplateFunctionalityTrait;

    /**
     * GeometricSequenceTemplateFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param GeometricSequenceTemplateRepository $repository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param DifficultyRepository $difficultyRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param TemplateJsonDataRepository $templateJsonDataRepository
     * @param TemplateJsonDataFunctionality $templateJsonDataFunctionality
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager,
        GeometricSequenceTemplateRepository $repository,
        ProblemTypeRepository $problemTypeRepository,
        ProblemConditionTypeRepository $problemConditionTypeRepository, ProblemConditionRepository $problemConditionRepository,
        DifficultyRepository $difficultyRepository, SubCategoryRepository $subCategoryRepository,
        TemplateJsonDataRepository $templateJsonDataRepository, TemplateJsonDataFunctionality $templateJsonDataFunctionality
    )
    {
        parent::__construct($entityManager);
        $this->problemTypeRepository = $problemTypeRepository;
        $this->problemConditionTypeRepository = $problemConditionTypeRepository;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->difficultyRepository = $difficultyRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->templateJsonDataRepository = $templateJsonDataRepository;
        $this->templateJsonDataFunctionality = $templateJsonDataFunctionality;
        $this->repository = $repository;
    }

    /**
     * @param iterable $data
     * @param bool $flush
     * @return BaseEntity|null
     * @throws \App\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     */
    public function create(iterable $data, bool $flush = true): ?BaseEntity
    {
        $entity = new GeometricSequenceTemplate();
        $entity = $this->setBasics($entity, $data);
        $entity->setIndexVariable($data->indexVariable);
        $entity->setFirstN($data->firstN);
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
     * @param bool $fromDataGrid
     * @return BaseEntity|null
     * @throws \App\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     */
    public function update(int $id, iterable $data, bool $flush = true, bool $fromDataGrid = false): ?BaseEntity
    {
        $entity = $this->baseUpdate($id, $data, $fromDataGrid);
        if(isset($data->indexVariable)){
            $entity->setIndexVariable($data->indexVariable);
        }
        if(isset($data->firstN)){
            $entity->setFirstN($data->firstN);
        }
        $this->em->persist($entity);
        if ($flush) {
            $this->em->flush();
        }
        return $entity;
    }
}
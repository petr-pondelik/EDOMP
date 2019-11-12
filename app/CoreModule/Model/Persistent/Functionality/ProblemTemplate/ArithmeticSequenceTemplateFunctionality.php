<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 23:39
 */

namespace App\CoreModule\Model\Persistent\Functionality\ProblemTemplate;

use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ArithmeticSequenceTemplate;
use App\CoreModule\Model\Persistent\Functionality\BaseFunctionality;
use App\CoreModule\Model\Persistent\Functionality\TemplateJsonDataFunctionality;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\ArithmeticSequenceTemplateRepository;
use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use App\CoreModule\Model\Persistent\Repository\SubCategoryRepository;
use App\CoreModule\Model\Persistent\Repository\TemplateJsonDataRepository;
use App\CoreModule\Model\Persistent\Traits\ProblemTemplateFunctionalityTrait;

/**
 * Class ArithmeticSequenceTemplateFunctionality
 * @package App\CoreModule\Model\Persistent\Functionality\ProblemTemplate
 */
class ArithmeticSequenceTemplateFunctionality extends BaseFunctionality
{
    use ProblemTemplateFunctionalityTrait;

    /**
     * ArithmeticSequenceTemplateFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param ArithmeticSequenceTemplateRepository $repository
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
        ArithmeticSequenceTemplateRepository $repository,
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
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     */
    public function create(iterable $data, bool $flush = true): ?BaseEntity
    {
        $entity = new ArithmeticSequenceTemplate();
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
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     */
    public function update(int $id, iterable $data, bool $flush = true, bool $fromDataGrid = false): ?BaseEntity
    {
        $entity = $this->baseUpdate($id, $data, $fromDataGrid);
        if(!empty($data->indexVariable)){
            $entity->setIndexVariable($data->indexVariable);
        }
        if(!empty($data->firstN)){
            $entity->setFirstN($data->firstN);
        }
        $this->em->persist($entity);
        if ($flush) {
            $this->em->flush();
        }
        return $entity;
    }
}
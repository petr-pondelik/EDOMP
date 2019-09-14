<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 23:39
 */

namespace App\Model\Persistent\Functionality\ProblemTemplate;

use App\Model\Persistent\Entity\BaseEntity;
use App\Model\Persistent\Entity\ProblemTemplate\ArithmeticSequenceTemplate;
use App\Model\Persistent\Functionality\BaseFunctionality;
use App\Model\Persistent\Functionality\TemplateJsonDataFunctionality;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\ProblemTemplate\ArithmeticSequenceTemplateRepository;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\ProblemConditionRepository;
use App\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Model\Persistent\Repository\TemplateJsonDataRepository;
use App\Model\Persistent\Traits\ProblemTemplateFunctionalityTrait;
use Nette\Utils\ArrayHash;

/**
 * Class ArithmeticSequenceTemplateFunctionality
 * @package App\Model\Persistent\Functionality
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
     * @param ArrayHash $data
     * @return BaseEntity|null
     * @throws \App\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     */
    public function create(ArrayHash $data): ?BaseEntity
    {
        $entity = new ArithmeticSequenceTemplate();
        $entity = $this->setBasics($entity, $data);
        $entity->setIndexVariable($data->indexVariable);
        $entity->setFirstN($data->firstN);
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @param bool $fromDataGrid
     * @return BaseEntity|null
     * @throws \App\Exceptions\EntityException
     * @throws \Nette\Utils\JsonException
     */
    public function update(int $id, ArrayHash $data, bool $fromDataGrid = false): ?BaseEntity
    {
        $entity = $this->baseUpdate($id, $data, $fromDataGrid);
        if(!empty($data->indexVariable)){
            $entity->setIndexVariable($data->indexVariable);
        }
        if(!empty($data->firstN)){
            $entity->setFirstN($data->firstN);
        }
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
    }
}
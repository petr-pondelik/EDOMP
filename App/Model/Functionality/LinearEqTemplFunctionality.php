<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 14:19
 */

namespace App\Model\Functionality;

use App\Model\Entity\LinearEqTempl;
use App\Model\Manager\ConstraintEntityManager;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\LinearEqTemplRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TemplateJsonDataRepository;
use App\Model\Traits\ProblemTemplateFunctionalityTrait;
use Nette\Utils\ArrayHash;

/**
 * Class LinearEqTemplFunctionality
 * @package App\Model\Functionality
 */
class LinearEqTemplFunctionality extends BaseFunctionality
{

    use ProblemTemplateFunctionalityTrait;

    /**
     * LinearEqTemplFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param LinearEqTemplRepository $repository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param DifficultyRepository $difficultyRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param TemplateJsonDataRepository $templateJsonDataRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager,
        LinearEqTemplRepository $repository,
        ProblemTypeRepository $problemTypeRepository, ProblemConditionRepository $problemConditionRepository,
        DifficultyRepository $difficultyRepository, SubCategoryRepository $subCategoryRepository,
        TemplateJsonDataRepository $templateJsonDataRepository
    )
    {
        parent::__construct($entityManager);
        $this->problemTypeRepository = $problemTypeRepository;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->difficultyRepository = $difficultyRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->templateJsonDataRepository = $templateJsonDataRepository;
        $this->repository = $repository;
    }

    /**
     * @param ArrayHash $data
     * @return Object|null
     * @throws \Exception
     */
    public function create(ArrayHash $data): ?Object
    {
        $entity = new LinearEqTempl();
        $entity = $this->setBaseValues($entity, $data);
        $entity->setVariable($data->variable);
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @param bool $fromDataGrid
     * @return Object
     * @throws \Exception
     */
    public function update(int $id, ArrayHash $data, bool $fromDataGrid = false): Object
    {
        $entity = $this->baseUpdate($id, $data, $fromDataGrid);
        if(!empty($data->variable)){
            $entity->setVariable($data->variable);
        }
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
    }
}
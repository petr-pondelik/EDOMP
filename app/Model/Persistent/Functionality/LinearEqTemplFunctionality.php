<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 14:19
 */

namespace App\Model\Persistent\Functionality;

use App\Model\Persistent\Entity\LinearEqTempl;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\LinearEqTemplRepository;
use App\Model\Persistent\Repository\ProblemConditionRepository;
use App\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Model\Persistent\Repository\TemplateJsonDataRepository;
use App\Model\Persistent\Traits\ProblemTemplateFunctionalityTrait;
use Nette\Utils\ArrayHash;

/**
 * Class LinearEqTemplFunctionality
 * @package App\Model\Persistent\Functionality
 */
class LinearEqTemplFunctionality extends BaseFunctionality
{
    use ProblemTemplateFunctionalityTrait;

    /**
     * LinearEqTemplFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param LinearEqTemplRepository $repository
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
        LinearEqTemplRepository $repository,
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
        bdump('UPDATE');
        bdump($id);
        $entity = $this->baseUpdate($id, $data, $fromDataGrid);
        if(!empty($data->variable)){
            $entity->setVariable($data->variable);
        }
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
    }
}
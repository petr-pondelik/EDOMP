<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 18:28
 */

namespace App\Model\Persistent\Functionality;

use App\Model\Persistent\Entity\GeometricSeqTempl;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\GeometricSeqTemplRepository;
use App\Model\Persistent\Repository\ProblemConditionRepository;
use App\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Model\Persistent\Repository\TemplateJsonDataRepository;
use App\Model\Persistent\Traits\ProblemTemplateFunctionalityTrait;
use Nette\Utils\ArrayHash;

/**
 * Class GeometricSeqTemplFunctionality
 * @package App\Model\Persistent\Functionality
 */
class GeometricSeqTemplFunctionality extends BaseFunctionality
{

    use ProblemTemplateFunctionalityTrait;

    /**
     * GeometricSeqTemplFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param GeometricSeqTemplRepository $repository
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
        GeometricSeqTemplRepository $repository,
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
        $templ = new GeometricSeqTempl();
        $templ = $this->setBaseValues($templ, $data);
        $templ->setVariable($data->variable);
        $templ->setFirstN($data->firstN);
        $this->em->persist($templ);
        $this->em->flush();
        return $templ;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @param bool $fromDataGrid
     * @return Object|null
     * @throws \Exception
     */
    public function update(int $id, ArrayHash $data, bool $fromDataGrid = false): ?Object
    {
        $templ = $this->baseUpdate($id, $data, $fromDataGrid);
        if(!empty($data->variable))
            $templ->setVariable($data->variable);
        if(!empty($data->first_n))
            $templ->setFirstN($data->firstN);
        $this->em->persist($templ);
        $this->em->flush();
        return $templ;
    }
}
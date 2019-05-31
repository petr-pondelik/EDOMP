<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 23:39
 */

namespace App\Model\Functionality;

use App\Model\Entity\ArithmeticSeqTempl;
use App\Model\Manager\ConstraintEntityManager;
use App\Model\Repository\ArithmeticSeqTemplRepository;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TemplateJsonDataRepository;
use App\Model\Traits\ProblemTemplateFunctionalityTrait;
use Nette\Utils\ArrayHash;

/**
 * Class ArithmeticSeqTemplFunctionality
 * @package App\Model\Functionality
 */
class ArithmeticSeqTemplFunctionality extends BaseFunctionality
{

    use ProblemTemplateFunctionalityTrait;

    /**
     * ArithmeticSeqTemplFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param ArithmeticSeqTemplRepository $repository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param DifficultyRepository $difficultyRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param TemplateJsonDataRepository $templateJsonDataRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager,
        ArithmeticSeqTemplRepository $repository,
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
        $templ = new ArithmeticSeqTempl();
        $templ = $this->setBaseValues($templ, $data);
        $templ->setVariable($data->variable);
        $templ->setFirstN($data->first_n);
        $this->em->persist($templ);
        $this->em->flush();
        return $templ;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @param bool $fromDataGrid
     * @return Object
     * @throws \Exception
     */
    public function update(int $id, ArrayHash $data, bool $fromDataGrid = false): ?Object
    {
        $templ = $this->baseUpdate($id, $data, $fromDataGrid);
        if(!empty($data->variable))
            $templ->setVariable($data->variable);
        if(!empty($data->first_n))
            $templ->setFirstN($data->first_n);
        $this->em->persist($templ);
        $this->em->flush();
        return $templ;
    }
}
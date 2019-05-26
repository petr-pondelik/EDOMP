<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 19:46
 */

namespace App\Model\Functionality;

use App\Model\Entity\QuadraticEqTempl;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\QuadraticEqTemplRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TemplateJsonDataRepository;
use App\Model\Traits\ProblemTemplateFunctionalityTrait;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\ArrayHash;

/**
 * Class QuadraticEqTemplFunctionality
 * @package App\Model\Functionality
 */
class QuadraticEqTemplFunctionality extends BaseFunctionality
{

    use ProblemTemplateFunctionalityTrait;

    /**
     * QuadraticEqTemplFunctionality constructor.
     * @param EntityManager $entityManager
     * @param QuadraticEqTemplRepository $repository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param DifficultyRepository $difficultyRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param TemplateJsonDataRepository $templateJsonDataRepository
     */
    public function __construct
    (
        EntityManager $entityManager,
        QuadraticEqTemplRepository $repository,
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
        $templ = new QuadraticEqTempl();
        $templ = $this->setBaseValues($templ, $data);
        $templ->setVariable($data->variable);
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
        $this->em->persist($templ);
        $this->em->flush();
        return $templ;
    }
}
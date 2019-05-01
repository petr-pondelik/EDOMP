<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 14:14
 */

namespace App\Model\Functionality;

use App\Model\Entity\ProblemTemplate;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemTemplateRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TemplateJsonDataRepository;
use App\Model\Traits\ProblemTemplateFunctionalityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemTemplateFunctionality
 * @package App\Model\Functionality
 */
class ProblemTemplateFunctionality extends BaseFunctionality
{

    use ProblemTemplateFunctionalityTrait;

    /**
     * ProblemFinalFunctionality constructor.
     * @param EntityManager $entityManager
     * @param ProblemTypeRepository $problemTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param DifficultyRepository $difficultyRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param TemplateJsonDataRepository $templateJsonDataRepository
     */
    public function __construct
    (
        EntityManager $entityManager,
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
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @param bool $fromDataGrid
     * @return Object
     */
    public function update(int $id, ArrayHash $data, bool $fromDataGrid = false): ?Object
    {
        $templ = $this->repository->find($id);
        if(!$fromDataGrid)
            $templ->setConditions(new ArrayCollection());
        return $this->setBaseValues($templ, $data, $id, $fromDataGrid);
    }

    /**
     * @param ArrayHash $data
     * @return int
     */
    public function create(ArrayHash $data): int
    {
        return 0;
    }
}
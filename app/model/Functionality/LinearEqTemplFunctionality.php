<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 14:19
 */

namespace App\Model\Functionality;

use App\Model\Entity\LinearEqTempl;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\LinearEqTemplRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TemplateJsonDataRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\ArrayHash;

/**
 * Class LinearEqTemplFunctionality
 * @package App\Model\Functionality
 */
class LinearEqTemplFunctionality extends ProblemTemplateFunctionality
{

    public function __construct(
        EntityManager $entityManager,
        LinearEqTemplRepository $repository,
        ProblemTypeRepository $problemTypeRepository, ProblemConditionRepository $problemConditionRepository,
        DifficultyRepository $difficultyRepository, SubCategoryRepository $subCategoryRepository,
        TemplateJsonDataRepository $templateJsonDataRepository
    )
    {
        parent::__construct(
            $entityManager,
            $problemTypeRepository, $problemConditionRepository, $difficultyRepository, $subCategoryRepository,
            $templateJsonDataRepository

        );
        $this->repository = $repository;
    }

    /**
     * @param ArrayHash $data
     * @throws \Exception
     */
    public function create(ArrayHash $data): void
    {
        $templ = new LinearEqTempl();
        $this->setBaseValues($templ, $data);
        $templ->setVariable($data->variable);
        $this->em->persist($templ);
        $this->em->flush();
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     */
    public function update(int $id, ArrayHash $data): void
    {
        // TODO: Implement update() method.
    }
}
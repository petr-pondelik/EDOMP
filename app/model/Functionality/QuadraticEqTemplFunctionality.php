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
use App\Model\Repository\SequenceInfoRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TemplateJsonDataRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\ArrayHash;

/**
 * Class QuadraticEqTemplFunctionality
 * @package App\Model\Functionality
 */
class QuadraticEqTemplFunctionality extends ProblemTemplateFunctionality
{

    public function __construct
    (
        EntityManager $entityManager,
        QuadraticEqTemplRepository $repository,
        ProblemTypeRepository $problemTypeRepository, ProblemConditionRepository $problemConditionRepository,
        DifficultyRepository $difficultyRepository, SubCategoryRepository $subCategoryRepository,
        TemplateJsonDataRepository $templateJsonDataRepository
    )
    {
        parent::__construct
        (
            $entityManager,
            $problemTypeRepository, $problemConditionRepository, $difficultyRepository, $subCategoryRepository,
            $templateJsonDataRepository

        );
        $this->repository = $repository;
    }

    /**
     * @param ArrayHash $data
     * @return void
     * @throws \Exception
     */
    public function create(ArrayHash $data): void
    {
        $templ = new QuadraticEqTempl();
        $templ = $this->setBaseValues($templ, $data);
        $templ->setVariable($data->variable);
        $this->em->persist($templ);
        $this->em->flush();
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @param bool|null $fromDataGrid
     * @return Object|null
     * @throws \Exception
     */
    public function update(int $id, ArrayHash $data, bool $fromDataGrid = null): ?Object
    {
        $templ = parent::update($id, $data, $fromDataGrid);
        if(!empty($data->variable))
            $templ->setVariable($data->variable);
        $this->em->persist($templ);
        $this->em->flush();
        return $templ;
    }
}
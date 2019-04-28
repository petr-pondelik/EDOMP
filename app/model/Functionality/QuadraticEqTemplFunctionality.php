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
        SequenceInfoFunctionality $sequenceInfoFunctionality,
        ProblemTypeRepository $problemTypeRepository, ProblemConditionRepository $problemConditionRepository,
        DifficultyRepository $difficultyRepository, SubCategoryRepository $subCategoryRepository,
        TemplateJsonDataRepository $templateJsonDataRepository, SequenceInfoRepository $sequenceInfoRepository
    )
    {
        parent::__construct
        (
            $entityManager,
            $sequenceInfoFunctionality,
            $problemTypeRepository, $problemConditionRepository, $difficultyRepository, $subCategoryRepository,
            $templateJsonDataRepository, $sequenceInfoRepository

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
        $this->sequenceInfoFunctionality->storeInfo(
            ArrayHash::from([
                "problemTemplateSeqVal" => $templ->getId()
            ])
        );
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
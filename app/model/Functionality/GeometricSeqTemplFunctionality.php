<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 18:28
 */

namespace App\Model\Functionality;

use App\Model\Entity\GeometricSeqTempl;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\GeometricSeqTemplRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SequenceInfoRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TemplateJsonDataRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\ArrayHash;

/**
 * Class GeometricSeqTemplFunctionality
 * @package App\Model\Functionality
 */
class GeometricSeqTemplFunctionality extends ProblemTemplateFunctionality
{

    public function __construct
    (
        EntityManager $entityManager,
        GeometricSeqTemplRepository $repository,
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
        $templ = new GeometricSeqTempl();
        $templ = $this->setBaseValues($templ, $data);
        $templ->setVariable($data->variable);
        $templ->setFirstN($data->first_n);
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
     * @param bool $fromDataGrid
     * @return Object|null
     * @throws \Exception
     */
    public function update(int $id, ArrayHash $data, bool $fromDataGrid = false): ?Object
    {
        $templ = parent::update($id, $data, $fromDataGrid);
        if(!empty($data->variable))
            $templ->setVariable($data->variable);
        if(!empty($data->first_n))
            $templ->setFirstN($data->first_n);
        $this->em->persist($templ);
        $this->em->flush();
        return $templ;
    }
}
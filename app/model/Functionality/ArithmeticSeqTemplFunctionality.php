<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 23:39
 */

namespace App\Model\Functionality;

use App\Model\Entity\ArithmeticSeqTempl;
use App\Model\Repository\ArithmeticSeqTemplRepository;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SequenceInfoRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TemplateJsonDataRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\ArrayHash;

/**
 * Class ArithmeticSeqTemplFunctionality
 * @package App\Model\Functionality
 */
class ArithmeticSeqTemplFunctionality extends ProblemTemplateFunctionality
{

    public function __construct
    (
        EntityManager $entityManager,
        ArithmeticSeqTemplRepository $repository,
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
        $templ = new ArithmeticSeqTempl();
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
     * @return Object
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
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 16:52
 */

namespace App\Model\Functionality;

use App\Model\Entity\Problem;
use App\Model\Entity\ProblemCondition;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemFunctionality
 * @package App\Model\Functionality
 */
class ProblemFunctionality extends BaseFunctionality
{

    /**
     * @var ProblemTypeRepository
     */
    protected $problemTypeRepository;

    /**
     * @var ProblemConditionRepository
     */
    protected $problemConditionRepository;

    /**
     * @var DifficultyRepository
     */
    protected $difficultyRepository;

    /**
     * @var SubCategoryRepository
     */
    protected $subCategoryRepository;

    /**
     * ProblemFunctionality constructor.
     * @param EntityManager $entityManager
     * @param ProblemRepository $problemRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param DifficultyRepository $difficultyRepository
     * @param SubCategoryRepository $subCategoryRepository
     */
    public function __construct
    (
        EntityManager $entityManager,
        ProblemRepository $problemRepository,
        ProblemTypeRepository $problemTypeRepository, ProblemConditionRepository $problemConditionRepository,
        DifficultyRepository $difficultyRepository, SubCategoryRepository $subCategoryRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $problemRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->difficultyRepository = $difficultyRepository;
        $this->subCategoryRepository = $subCategoryRepository;
    }

    /**
     * @param ArrayHash $data
     * @throws \Exception
     */
    public function create(ArrayHash $data): void
    {
        $problem = new Problem();

        $problem->setTextBefore($data->text_before);
        $problem->setBody($data->body);
        $problem->setTextAfter($data->text_after);
        $problem->setResult($data->result);
        $problem->setProblemType($this->problemTypeRepository->find($data->type));
        $problem->setDifficulty($this->difficultyRepository->find($data->difficulty));
        $problem->setSubCategory($this->subCategoryRepository->find($data->subcategory));
        $problem = $this->attachConditions($problem, $data);

        $this->em->persist($problem);
        $this->em->flush();
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @param bool $updateConditions
     * @throws \Exception
     */
    public function update(int $id, ArrayHash $data, bool $updateConditions = true): void
    {
        $problem = $this->repository->find($id);

        if(!empty($data->text_before))
            $problem->setTextBefore($data->text_before);
        if(!empty($data->body))
            $problem->setBody($data->body);
        if(!empty($data->text_after))
            $problem->setTextAfter($data->text_after);
        if(!empty($data->result))
            $problem->setResult($data->result);

        if(!empty($data->type))
            $problem->setProblemType($this->problemTypeRepository->find($data->type));
        if(!empty($data->subcategory))
            $problem->setSubCategory($this->subCategoryRepository->find($data->subcategory));
        if(!empty($data->difficulty))
            $problem->setDifficulty($this->difficultyRepository->find($data->difficulty));

        if($updateConditions){
            $problem->setConditions(new ArrayCollection());
            $this->attachConditions($problem, $data);
        }

        $this->em->persist($problem);
        $this->em->flush();
    }

    /**
     * @param Problem $problem
     * @param ArrayHash $data
     * @return Problem
     */
    public function attachConditions(Problem $problem, ArrayHash $data)
    {
        $type = $this->problemTypeRepository->find($data->type);
        $problemCondTypes = $type->getConditionTypes()->getValues();

        foreach ($problemCondTypes as $problemCondType){

            //Get ConditionType ID
            $condTypeId = $problemCondType->getId();

            //Get ConditionType value from created problem
            $condTypeVal = $data->{"condition_" . $condTypeId};

            $condition = $this->problemConditionRepository->findOneBy([
                "problemConditionType.id" => $condTypeId,
                "accessor" => $condTypeVal
            ]);

            $problem->addCondition($condition);

        }

        return $problem;
    }
}
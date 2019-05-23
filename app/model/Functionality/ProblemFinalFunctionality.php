<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 16:52
 */

namespace App\Model\Functionality;

use App\Helpers\FormatterHelper;
use App\Model\Entity\ProblemFinal;
use App\Model\Entity\ProblemCondition;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemFinalRepository;
use App\Model\Repository\ProblemRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemFinalFunctionality
 * @package App\Model\Functionality
 */
class ProblemFinalFunctionality extends BaseFunctionality
{

    /**
     * @var ProblemRepository
     */
    protected $problemRepository;

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
     * @var FormatterHelper
     */
    protected $formatterHelper;

    /**
     * ProblemFinalFunctionality constructor.
     * @param EntityManager $entityManager
     * @param ProblemFinalRepository $repository
     * @param ProblemRepository $problemRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param DifficultyRepository $difficultyRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param FormatterHelper $formatterHelper
     */
    public function __construct
    (
        EntityManager $entityManager,
        ProblemFinalRepository $repository,
        ProblemRepository $problemRepository,
        ProblemTypeRepository $problemTypeRepository, ProblemConditionRepository $problemConditionRepository,
        DifficultyRepository $difficultyRepository, SubCategoryRepository $subCategoryRepository,
        FormatterHelper $formatterHelper
    )
    {
        parent::__construct($entityManager);
        $this->repository = $repository;
        $this->problemRepository = $problemRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->difficultyRepository = $difficultyRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->formatterHelper = $formatterHelper;
    }

    /**
     * @param ArrayHash $data
     * @param array|null $conditions
     * @return int
     * @throws \Exception
     */
    public function create(ArrayHash $data, array $conditions = null): int
    {
        $problem = new ProblemFinal();

        $problem->setTextBefore($data->text_before);
        $problem->setBody($data->body);
        $problem->setTextAfter($data->text_after);

        if(isset($data->result))
            $problem->setResult($data->result);
        if(isset($data->is_generated))
            $problem->setIsGenerated($data->is_generated);
        if(isset($data->variable))
            $problem->setVariable($data->variable);
        if(isset($data->firstN))
            $problem->setFirstN($data->firstN);

        $problem->setProblemType($this->problemTypeRepository->find($data->type));
        $problem->setDifficulty($this->difficultyRepository->find($data->difficulty));
        $problem->setSubCategory($this->subCategoryRepository->find($data->subcategory));
        if(isset($data->problem_template_id))
            $problem->setProblemTemplate($this->problemRepository->find($data->problem_template_id));

        if($conditions === null)
            $problem = $this->attachConditions($problem, $data);
        else
            $problem->setConditions($conditions);

        $this->em->persist($problem);
        $this->em->flush();

        return $problem->getId();
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @param bool $updateConditions
     * @return Object|null
     * @throws \Exception
     */
    public function update(int $id, ArrayHash $data, bool $updateConditions = true): ?Object
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

        return $problem;
    }

    /**
     * @param ProblemFinal $problem
     * @param ArrayHash $data
     * @return ProblemFinal
     */
    public function attachConditions(ProblemFinal $problem, ArrayHash $data): ProblemFinal
    {
        bdump($data);
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

    /**
     * @param int $id
     * @param ArrayHash $result
     * @throws \Exception
     */
    public function storeResult(int $id, ArrayHash $result)
    {
        $problem = $this->repository->find($id);
        $result = $this->formatterHelper->formatResult($result);
        $problem->setResult($result);
        $this->em->persist($problem);
        $this->em->flush();
    }
}
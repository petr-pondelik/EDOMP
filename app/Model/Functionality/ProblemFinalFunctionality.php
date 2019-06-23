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
use App\Model\Manager\ConstraintEntityManager;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemFinalRepository;
use App\Model\Repository\ProblemRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
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
     * @param ConstraintEntityManager $entityManager
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
        ConstraintEntityManager $entityManager,
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
     * @param bool $flush
     * @return Object|null
     * @throws \Exception
     */
    public function create(ArrayHash $data, array $conditions = null, bool $flush = true): ?Object
    {
        $problem = new ProblemFinal();
        $problem->setTextBefore($data->textBefore);
        $problem->setBody($data->body);
        $problem->setTextAfter($data->textAfter);

        if(isset($data->result)){
            $problem->setResult($data->result);
        }
        if(isset($data->is_generated)){
            $problem->setIsGenerated($data->is_generated);
        }
        if(isset($data->variable)){
            $problem->setVariable($data->variable);
        }
        if(isset($data->first_n)){
            $problem->setFirstN($data->first_n);
        }
        if(isset($data->created)){
            $problem->setCreated($data->created);
        }

        $problem->setProblemType($this->problemTypeRepository->find($data->problemFinalType));
        $problem->setDifficulty($this->difficultyRepository->find($data->difficulty));
        $problem->setSubCategory($this->subCategoryRepository->find($data->subCategory));

        if(isset($data->problem_template_id)){
            $problem->setProblemTemplate($this->problemRepository->find($data->problem_template_id));
        }
        if($conditions === null){
            $problem = $this->attachConditions($problem, $data);
        }
        else{
            $problem->setConditions($conditions);
        }

        $this->em->persist($problem);

        if($flush){
            $this->em->flush();
        }

        return $problem;
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

        if(!$problem){
            throw new EntityNotFoundException('Entity for update not found.');
        }

        if(!empty($data->textBefore)){
            $problem->setTextBefore($data->textBefore);
        }
        if(!empty($data->body)){
            $problem->setBody($data->body);
        }
        if(!empty($data->textAfter)){
            $problem->setTextAfter($data->textAfter);
        }
        if(!empty($data->result)){
            $problem->setResult($data->result);
        }
        if(isset($data->created)){
            $problem->setCreated($data->created);
        }

        if(!empty($data->problemFinalType)){
            $problem->setProblemType($this->problemTypeRepository->find($data->problemFinalType));
        }
        if(!empty($data->subCategory)){
            $problem->setSubCategory($this->subCategoryRepository->find($data->subCategory));
        }
        if(!empty($data->difficulty)){
            $problem->setDifficulty($this->difficultyRepository->find($data->difficulty));
        }

        if($updateConditions && $data->problemFinalType){
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
        $type = $this->problemTypeRepository->find($data->problemFinalType);
        $problemCondTypes = $type->getConditionTypes()->getValues();

        foreach ($problemCondTypes as $problemCondType){

            //Get ConditionType ID
            $condTypeId = $problemCondType->getId();

            //Get ConditionType value from created problem
            $condTypeVal = $data->{'condition_' . $condTypeId};

            $condition = $this->problemConditionRepository->findOneBy([
                'problemConditionType.id' => $condTypeId,
                'accessor' => $condTypeVal
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
    public function storeResult(int $id, ArrayHash $result): void
    {
        $problem = $this->repository->find($id);
        if(!$problem){
            throw new EntityNotFoundException('Entity for update not found.');
        }
        $result = $this->formatterHelper->formatResult($result);
        $problem->setResult($result);
        $this->em->persist($problem);
        $this->em->flush();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 11.9.19
 * Time: 21:50
 */

namespace App\Model\Persistent\Traits;

use App\CoreModule\Helpers\FormatterHelper;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\ProblemConditionRepository;
use App\Model\Persistent\Repository\ProblemRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;

/**
 * Trait ProblemFinalFunctionalityTrait
 * @package App\Model\Persistent\Traits
 */
trait ProblemFinalFunctionalityTrait
{
    use ProblemFunctionalityTrait;

    /**
     * @var ProblemRepository
     */
    protected $problemRepository;

    /**
     * @var FormatterHelper
     */
    protected $formatterHelper;

    /**
     * @param ProblemTypeRepository $problemTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param DifficultyRepository $difficultyRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemRepository $problemRepository
     * @param FormatterHelper $formatterHelper
     */
    public function injectRepositories
    (
        ProblemTypeRepository $problemTypeRepository,
        ProblemConditionRepository $problemConditionRepository,
        DifficultyRepository $difficultyRepository,
        SubCategoryRepository $subCategoryRepository,
        ProblemRepository $problemRepository,
        FormatterHelper $formatterHelper
    ): void
    {
        $this->problemTypeRepository = $problemTypeRepository;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->difficultyRepository = $difficultyRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->problemRepository = $problemRepository;
        $this->formatterHelper = $formatterHelper;
    }

    /**
     * @param ProblemFinal $entity
     * @param ArrayHash $data
     * @return ProblemFinal
     */
    public function setBasics(ProblemFinal $entity, ArrayHash $data): ProblemFinal
    {
        bdump('SET BASICS');
        bdump($data);

        if(isset($data->body)){
            $entity->setBody($data->body);
        }

        if(isset($data->problemType)){
            $entity->setProblemType($this->problemTypeRepository->find($data->problemType));
        }

        if(isset($data->difficulty)){
            $entity->setDifficulty($this->difficultyRepository->find($data->difficulty));
        }

        if(isset($data->subCategory)){
            $entity->setSubCategory($this->subCategoryRepository->find($data->subCategory));
        }

        if(isset($data->result)){
            $entity->setResult($data->result);
        }

        if(isset($data->textBefore)){
            $entity->setTextBefore($data->textBefore);
        }

        if(isset($data->textAfter)){
            $entity->setTextAfter($data->textAfter);
        }

        if(isset($data->problemTemplateId)){
            $entity->setProblemTemplate($this->problemRepository->find($data->problemTemplateId));
        }

        $entity->setIsGenerated( isset($data->isGenerated) && $data->isGenerated );

        if(isset($data->matchesIndex)){
            $entity->setMatchesIndex($data->matchesIndex);
        }

        return $entity;
    }

    /**
     * @param ProblemFinal $problem
     * @param ArrayHash $data
     * @return ProblemFinal
     */
    public function attachConditions(ProblemFinal $problem, ArrayHash $data): ProblemFinal
    {
        if(isset($data->problemType)){
            $type = $this->problemTypeRepository->find($data->problemType);
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
        $formatted = $this->formatterHelper->formatResult($result);
        $problem->setResult($formatted);
        $this->em->persist($problem);
        $this->em->flush();
    }
}
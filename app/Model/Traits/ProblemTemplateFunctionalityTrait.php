<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 12:53
 */

namespace App\Model\Traits;

use App\Model\Entity\ProblemTemplate;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TemplateJsonDataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Utils\ArrayHash;

/**
 * Trait ProblemTemplateFunctionalityTrait
 * @package App\Model\Traits
 */
trait ProblemTemplateFunctionalityTrait
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
     * @var TemplateJsonDataRepository
     */
    protected $templateJsonDataRepository;

    /**
     * @param $templ
     * @param ArrayHash $data
     * @param int|null $templateId
     * @param bool $fromDataGrid
     * @return ProblemTemplate
     */
    public function setBaseValues($templ, ArrayHash $data, int $templateId = null, bool $fromDataGrid = false): ProblemTemplate
    {
        if(isset($data->text_before)){
            $templ->setTextBefore($data->text_before);
        }
        if(isset($data->body)){
            $templ->setBody($data->body);
        }
        if(isset($data->text_after)){
            $templ->setTextAfter($data->text_after);
        }
        if(isset($data->type)){
            $templ->setProblemType($this->problemTypeRepository->find($data->type));
        }
        if(isset($data->difficulty)){
            $templ->setDifficulty($this->difficultyRepository->find($data->difficulty));
        }
        if(isset($data->subCategory)){
            $templ->setSubCategory($this->subCategoryRepository->find($data->subCategory));
        }
        if(isset($data->matches)){
            $templ->setMatches($data->matches);
        }
        if(isset($data->created)){
            $templ->setCreated($data->created);
        }

        if(!$fromDataGrid){
            $attached = $this->attachConditions($templ, $data);
            $templ = $attached->template;

            if(!$templateId){
                $templateId = $this->repository->getSequenceVal();
            }

            $templJsonData = null;

            if($jsonDataObj = $this->templateJsonDataRepository->findOneBy(['templateId' => $templateId])){
                $templJsonData = $jsonDataObj->getJsonData();
            }

            if($attached->hasCondition){
                $templ->setMatches($templJsonData);
            }
            else{
                $templ->setMatches(null);
            }
        }

        return $templ;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @param bool $fromDataGrid
     * @return Object|null
     */
    public function baseUpdate(int $id, ArrayHash $data, bool $fromDataGrid = false): ?Object
    {
        $templ = $this->repository->find($id);
        if(!$fromDataGrid){
            $templ->setConditions(new ArrayCollection());
        }
        return $this->setBaseValues($templ, $data, $id, $fromDataGrid);
    }

    /**
     * @param $templ
     * @param ArrayHash $data
     * @return ArrayHash
     */
    protected function attachConditions($templ, ArrayHash $data): ArrayHash
    {
        $hasCondition = false;

        $type = $this->problemTypeRepository->find($data->type);
        $problemCondTypes = $type->getConditionTypes()->getValues();

        foreach ($problemCondTypes as $problemCondType){

            //Get ConditionType ID
            $condTypeId = $problemCondType->getId();

            //Get ConditionType value from created problem
            $condTypeVal = $data->{'condition_' . $condTypeId};

            //Template has condition
            if($condTypeVal){
                $hasCondition = true;
            }

            $condition = $this->problemConditionRepository->findOneBy([
                'problemConditionType.id' => $condTypeId,
                'accessor' => $condTypeVal
            ]);

            $templ->addCondition($condition);

        }

        return ArrayHash::from([
            'template' => $templ,
            'hasCondition' => $hasCondition
        ]);
    }
}
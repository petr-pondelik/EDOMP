<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 12:53
 */

namespace App\Model\Traits;

use App\Model\Entity\ProblemTemplate;
use App\Model\Functionality\TemplateJsonDataFunctionality;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemConditionTypeRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TemplateJsonDataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;

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
     * @var ProblemConditionTypeRepository
     */
    protected $problemConditionTypeRepository;

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
     * @var TemplateJsonDataFunctionality
     */
    protected $templateJsonDataFunctionality;

    /**
     * @param array $firstArr
     * @param array $secondArr
     * @return array
     */
    public function intersectJsonDataArrays(array $firstArr, array $secondArr): array
    {
        return array_uintersect($firstArr, $secondArr, static function($first, $second) {
            return strcmp(serialize($first), serialize($second));
        });
    }

    /**
     * @param $templ
     * @param ArrayHash $data
     * @param int|null $templateId
     * @param bool $fromDataGrid
     * @return ProblemTemplate
     * @throws \Nette\Utils\JsonException
     */
    public function setBaseValues($templ, ArrayHash $data, int $templateId = null, bool $fromDataGrid = false): ProblemTemplate
    {
        bdump('SET BASE VALUES');

        if(isset($data->textBefore)){
            $templ->setTextBefore($data->textBefore);
        }
        if(isset($data->body)){
            $templ->setBody($data->body);
        }
        if(isset($data->textAfter)){
            $templ->setTextAfter($data->textAfter);
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

            bdump($templateId);

            $templateJsonData = [];

            if($templateJsons = $this->templateJsonDataRepository->findBy(['templateId' => $templateId])){
                $templateJsonData = Json::decode($templateJsons[0]->getJsonData());
                // Unset picked template JSON
                unset($templateJsons[0]);
                // Make merge of all template recorded JSONs
                foreach ($templateJsons as $json){
                    $arr = Json::decode($json->getJsonData());
                    $templateJsonData = $this->intersectJsonDataArrays($templateJsonData, $arr);
                }
            }

            // Reindex array key to start from 0 (array_values) and encode data to JSON string
            if($templateJsonData){
                $templateJsonData = Json::encode(array_values($templateJsonData));
            }

            $templ->setMatches($templateJsonData);

            // Comment this for testing purposes
            $this->templateJsonDataFunctionality->deleteByTemplate($templateId);
        }

        return $templ;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @param bool $fromDataGrid
     * @return Object|null
     * @throws \Nette\Utils\JsonException
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
        $problemCondTypes = $this->problemConditionTypeRepository->findNonValidation($data->type);

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
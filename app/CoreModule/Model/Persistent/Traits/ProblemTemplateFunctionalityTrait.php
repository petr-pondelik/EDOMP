<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 12:53
 */

namespace App\CoreModule\Model\Persistent\Traits;

use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use App\CoreModule\Model\Persistent\Functionality\TemplateJsonDataFunctionality;
use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use App\CoreModule\Model\Persistent\Repository\SubCategoryRepository;
use App\CoreModule\Model\Persistent\Repository\TemplateJsonDataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;

/**
 * Trait ProblemTemplateFunctionalityTrait
 * @package App\CoreModule\Model\Persistent\Traits
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
     * @param $template
     * @param ArrayHash $data
     * @param int|null $templateId
     * @param bool $fromDataGrid
     * @return ProblemTemplate
     * @throws \Nette\Utils\JsonException
     */
    public function setBasics($template, ArrayHash $data, int $templateId = null, bool $fromDataGrid = false): ProblemTemplate
    {
        bdump('SET BASE VALUES');
        bdump($data);

        if(isset($data->textBefore)){
            $template->setTextBefore($data->textBefore);
        }
        if(isset($data->body)){
            $template->setBody($data->body);
        }
        if(isset($data->textAfter)){
            $template->setTextAfter($data->textAfter);
        }
        if(isset($data->type)){
            $template->setProblemType($this->problemTypeRepository->find($data->type));
        }
        if(isset($data->difficulty)){
            $template->setDifficulty($this->difficultyRepository->find($data->difficulty));
        }
        if(isset($data->subCategory)){
            $template->setSubCategory($this->subCategoryRepository->find($data->subCategory));
        }
        if(isset($data->matches)){
            $template->setMatches($data->matches);
        }

        if(!$fromDataGrid){
            $attached = $this->attachConditions($template, $data);
            $template = $attached->template;

            bdump($templateId);

            if(!$templateId){
                $templateId = $this->repository->getSequenceVal();
            }

            $templateJsonData = [];

            bdump('BEFORE TEMPLATE MATCHES INTERSECT');
            bdump($templateId);

            if($templateJsons = $this->templateJsonDataRepository->findBy(['templateId' => $templateId])){

                $templateJsonData = Json::decode($templateJsons[0]->getJsonData());
                bdump($templateJsonData);

                // Unset picked template JSON
                unset($templateJsons[0]);

                // Make merge of all template recorded JSONs
                foreach ($templateJsons as $json){
                    $problemConditionTypeId = $json->getProblemConditionType()->getId();
                    if( isset($data->{'condition_' . $problemConditionTypeId}) && $data->{'condition_' . $problemConditionTypeId} !== 0) {
                        $arr = Json::decode($json->getJsonData());
                        bdump($arr);
                        $templateJsonData = $this->intersectJsonDataArrays($templateJsonData, $arr);
                    }
                }

            }

            if ($templateJsonData) {
                // Reindex array key to start from 0 (array_values) and encode data to JSON string
                $templateJsonData = Json::encode(array_values($templateJsonData));
                bdump($templateJsonData);
                $template->setMatches($templateJsonData);
            }

            // Comment this for testing purposes
            $this->templateJsonDataFunctionality->deleteByTemplate($templateId);

        }

        return $template;
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
        $template = $this->repository->find($id);
        if(!$fromDataGrid){
            $template->setConditions(new ArrayCollection());
        }
        return $this->setBasics($template, $data, $id, $fromDataGrid);
    }

    /**
     * @param $template
     * @param ArrayHash $data
     * @return ArrayHash
     */
    protected function attachConditions($template, ArrayHash $data): ArrayHash
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

            $template->addCondition($condition);
        }

        return ArrayHash::from([
            'template' => $template,
            'hasCondition' => $hasCondition
        ]);
    }
}
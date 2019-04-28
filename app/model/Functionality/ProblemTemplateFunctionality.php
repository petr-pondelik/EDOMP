<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 14:14
 */

namespace App\Model\Functionality;

use App\Model\Entity\ProblemTemplate;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemTemplateRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\SequenceInfoRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Model\Repository\TemplateJsonDataRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemTemplateFunctionality
 * @package App\Model\Functionality
 */
abstract class ProblemTemplateFunctionality extends BaseFunctionality
{

    /**
     * @var SequenceInfoFunctionality
     */
    protected $sequenceInfoFunctionality;

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
     * @var SequenceInfoRepository
     */
    protected $sequenceInfoRepository;

    /**
     * ProblemFunctionality constructor.
     * @param EntityManager $entityManager
     * @param SequenceInfoFunctionality $sequenceInfoFunctionality
     * @param ProblemTypeRepository $problemTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param DifficultyRepository $difficultyRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param TemplateJsonDataRepository $templateJsonDataRepository
     * @param SequenceInfoRepository $sequenceInfoRepository
     */
    public function __construct
    (
        EntityManager $entityManager,
        SequenceInfoFunctionality $sequenceInfoFunctionality,
        ProblemTypeRepository $problemTypeRepository, ProblemConditionRepository $problemConditionRepository,
        DifficultyRepository $difficultyRepository, SubCategoryRepository $subCategoryRepository,
        TemplateJsonDataRepository $templateJsonDataRepository, SequenceInfoRepository $sequenceInfoRepository
    )
    {
        parent::__construct($entityManager);
        $this->sequenceInfoFunctionality = $sequenceInfoFunctionality;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->difficultyRepository = $difficultyRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->templateJsonDataRepository = $templateJsonDataRepository;
        $this->sequenceInfoRepository = $sequenceInfoRepository;
    }

    /**
     * @param $templ
     * @param ArrayHash $data
     * @param int|null $templateId
     * @return ProblemTemplate
     */
    public function setBaseValues($templ, ArrayHash $data, int $templateId = null)
    {
        $templ->setTextBefore($data->text_before);
        $templ->setBody($data->body);
        $templ->setTextAfter($data->text_after);
        $templ->setProblemType($this->problemTypeRepository->find($data->type));
        $templ->setDifficulty($this->difficultyRepository->find($data->difficulty));
        $templ->setSubCategory($this->subCategoryRepository->find($data->subcategory));

        if(!$templateId)
            $templateId = $this->sequenceInfoRepository->find(1)->getProblemTemplateSeqVal() + 1;

        bdump($templateId);

        bdump($this->templateJsonDataRepository->findOneBy([ "templateId" => $templateId]));

        $templJsonData = null;

        if($jsonDataObj = $this->templateJsonDataRepository->findOneBy([ "templateId" => $templateId]))
            $templJsonData = $jsonDataObj->getJsonData();

        $templ->setMatches($templJsonData);

        bdump("ATTACH CONDITIONS");

        $templ = $this->attachConditions($templ, $data);

        return $templ;
    }

    /**
     * @param $templ
     * @param ArrayHash $data
     * @return ProblemTemplate
     */
    public function attachConditions($templ, ArrayHash $data)
    {
        bdump($templ);
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

            $templ->addCondition($condition);

        }

        return $templ;
    }
}
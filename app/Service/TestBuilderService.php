<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.3.19
 * Time: 9:46
 */

namespace App\Service;

use App\Model\Entity\ProblemFinal;
use App\Model\Functionality\LogoFunctionality;
use App\Model\Functionality\ProblemFinalFunctionality;
use App\Model\Functionality\ProblemTemplateFunctionality;
use App\Model\Functionality\TestFunctionality;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\LogoRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemFinalRepository;
use App\Model\Repository\ProblemRepository;
use App\Model\Repository\ProblemTemplateRepository;
use App\Model\Repository\TermRepository;
use App\Model\Repository\TestRepository;
use Dibi\UniqueConstraintViolationException;
use Nette\NotSupportedException;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class TestBuilderService
 * @package App\Services
 */
class TestBuilderService
{
    /**
     * @var TermRepository
     */
    protected $termRepository;

    /**
     * @var ProblemRepository
     */
    protected $problemRepository;

    /**
     * @var ProblemTemplateRepository
     */
    protected $problemTemplateRepository;

    /**
     * @var ProblemTemplateFunctionality
     */
    protected $problemTemplateFunctionality;

    /**
     * @var ProblemFinalFunctionality
     */
    protected $problemFinalFunctionality;

    /**
     * @var ProblemConditionRepository
     */
    protected $problemConditionRepository;

    /**
     * @var LogoRepository
     */
    protected $logoRepository;

    /**
     * @var LogoFunctionality
     */
    protected $logoFunctionality;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @var TestFunctionality
     */
    protected $testFunctionality;

    /**
     * @var GeneratorService
     */
    protected $generatorService;

    /**
     * @var array
     */
    protected $testVariantsLabels;

    /**
     * TestBuilderService constructor.
     * @param TermRepository $termRepository
     * @param ProblemRepository $problemRepository
     * @param ProblemTemplateRepository $problemTemplateRepository
     * @param ProblemTemplateFunctionality $problemTemplateFunctionality
     * @param ProblemFinalFunctionality $problemFinalFunctionality
     * @param ProblemConditionRepository $problemConditionRepository
     * @param LogoRepository $logoRepository
     * @param LogoFunctionality $logoFunctionality
     * @param GroupRepository $groupRepository
     * @param TestFunctionality $testFunctionality
     * @param GeneratorService $generatorService
     */
    public function __construct
    (
        TermRepository $termRepository, ProblemRepository $problemRepository,
        ProblemTemplateRepository $problemTemplateRepository, ProblemTemplateFunctionality $problemTemplateFunctionality,
        ProblemFinalFunctionality $problemFinalFunctionality, ProblemConditionRepository $problemConditionRepository,
        LogoRepository $logoRepository, LogoFunctionality $logoFunctionality,
        GroupRepository $groupRepository, TestFunctionality $testFunctionality,
        GeneratorService $generatorService
    )
    {
        $this->termRepository = $termRepository;
        $this->problemRepository = $problemRepository;
        $this->problemTemplateRepository = $problemTemplateRepository;
        $this->problemTemplateFunctionality = $problemTemplateFunctionality;
        $this->problemFinalFunctionality = $problemFinalFunctionality;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->logoRepository = $logoRepository;
        $this->logoFunctionality = $logoFunctionality;
        $this->groupRepository = $groupRepository;
        $this->testFunctionality = $testFunctionality;
        $this->generatorService = $generatorService;

        $this->testVariantsLabels = [
            0 => 'A',
            1 => 'B',
            2 => 'C',
            3 => 'D',
            4 => 'E',
            5 => 'F',
            6 => 'G',
            7 => 'H',
            8 => 'I'
        ];
    }

    /**
     * @param ArrayHash $data
     * @return array
     */
    public function testVariantsToArray(ArrayHash $data)
    {
        $variants = [];

        for($i = 0; $i < $data->variants; $i++)
            array_push($variants, $this->testVariantsLabels[$i]);

        return $variants;
    }

    /**
     * @param ArrayHash $data
     * @return ArrayHash|null
     */
    public function getLogoData(ArrayHash $data)
    {
        $logoId = $data->logo_file_hidden;

        $logo = $this->logoRepository->find($logoId);
        $logoPath = './' . Strings::substring($logo->getPath(), Strings::indexOf($logo->getPath(), DIRECTORY_SEPARATOR, -1)+1);

        $resArr = [
            "logoId" => $logoId,
            "logoPath" => $logoPath
        ];

        return ArrayHash::from($resArr);
    }

    /**
     * @param string $variant
     * @param ArrayHash $data
     * @param string|null $logoPath
     * @return array
     */
    public function buildVariantHead(string $variant, ArrayHash $data, string $logoPath = null)
    {
        $testHeader = [];

        $testHeader["variant"] = $variant;
        if($logoPath)
            $testHeader["logo"] = $logoPath;
        $testHeader["group"] = $this->groupRepository->find($data->group)->getLabel();
        $testHeader["test_term"] = $this->termRepository->find($data->test_term)->getLabel();
        $testHeader["school_year"] = $data->school_year;
        $testHeader["test_number"] = $data->test_number;

        return $testHeader;
    }

    /**
     * @param int $testId
     * @param string $variant
     * @param ArrayHash $data
     * @return mixed
     * @throws \Nette\Utils\JsonException
     */
    public function buildVariantBody(int $testId, string $variant, ArrayHash $data)
    {
        $body = [];
        $body["introduction_text"] = $data->introduction_text;
        for($i = 0; $i < $data->problems_cnt; $i++){

            $problemPrototypeId = null;
            $problemId = $data['problem_'.$i];
            $problem = $this->problemRepository->find($problemId);

            $body['problems'][$i]['before'] = $problem->getTextBefore();

            //If the problem is prototype, it needs to be generated to it's final form
            if($problem->isTemplate()){

                $generatedFinal = $this->generatorService->generateWithConditions($problem);

                //Build final problem object
                $prototypeData = new ArrayHash();
                $prototypeData["before"] = $problem->getTextBefore();
                $prototypeData["structure"] = $generatedFinal;
                $prototypeData["after"] = $problem->getTextAfter();
                $prototypeData["difficulty"] = $problem->difficulty_id;
                $prototypeData["type"] = $problem->problem_type_id;
                $prototypeData["result"] = "";
                $prototypeData["subcategory"] = $problem->sub_category_id;
                $prototypeData["first_n"] = $problem->first_n;
                $prototypeData["variable"] = $problem->variable;

                //Get prototype conditions
                $prototypeConditions = $problem->getConditions();

                //Store generated final problem to DB and switch problemId to it's ID
                $problemPrototypeId = $problemId;

                //TODO: CREATE FINAL PROBLEM CORRESPONDING

                $problemId = $this->problemFinalFunctionality->create($prototypeData, $prototypeConditions);
                //$this->problemManager->update($problemPrototypeId, ['is_used' => true]);

                $body['problems'][$i]['body'] = $generatedFinal;

            }
            else{
                $body['problems'][$i]['body'] = $problem->getBody();
            }

            $body['problems'][$i]['after'] = $problem->getTextAfter();
            $body["problems"][$i]["newpage"] = $data->{"newpage_" . $i};

            //Attach current problem to the created test
            $this->testFunctionality->attachProblem($testId, $variant, $problemId, $problemPrototypeId ?? null, $data->{"newpage_" . $i});
        }

        return $body;
    }

    /**
     * @param int $testId
     * @param string $variant
     * @param ArrayHash $data
     * @param $logoData
     * @return array
     * @throws \Nette\Utils\JsonException
     */
    public function buildTestVariant(int $testId, string $variant, ArrayHash $data, $logoData)
    {
        $variantData = [];

        $head = $this->buildVariantHead($variant, $data, $logoData ? $logoData->logoPath : null);
        $variantData['head'] = $head;

        $body = $this->buildVariantBody($testId, $variant, $data);
        $variantData['body'] = $body;

        return $variantData;
    }

    /**
     * @param ArrayHash $data
     * @return bool|ArrayHash
     * @throws \Nette\Utils\JsonException
     */
    public function buildTest(ArrayHash $data)
    {
        $test = [];

        $variants = $this->testVariantsToArray($data);
        $logoData = $this->getLogoData($data);

        bdump($variants);

        $testId = $this->testFunctionality->create(ArrayHash::from([
            "logo_id" => $logoData->logoId,
            "term_id" => $data->test_term,
            "school_year" => $data->school_year,
            "test_number" => $data->test_number,
            "group_id" => $data->group
        ]));

        $this->logoFunctionality->update($logoData->logoId, ArrayHash::from([
            "is_used" => true
        ]));

        foreach($variants as $variant){
            //Catch if there is one final problem more than once
            try{
                $test[$variant] = $this->buildTestVariant($testId, $variant, $data, $logoData);
            }
            catch(UniqueConstraintViolationException $e){
                throw new NotSupportedException('V testu se vyskytují dvě shodné úlohy.');
            }
        }

        bdump($test);

        $resArr = [
            "testId" => $testId,
            "test" => $test
        ];

        return ArrayHash::from($resArr);
    }

}
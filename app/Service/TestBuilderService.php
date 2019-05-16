<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.3.19
 * Time: 9:46
 */

namespace App\Service;

use App\Model\Entity\Test;
use App\Model\Functionality\LogoFunctionality;
use App\Model\Functionality\ProblemFinalFunctionality;
use App\Model\Functionality\ProblemTemplateFunctionality;
use App\Model\Functionality\TestFunctionality;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\LogoRepository;
use App\Model\Repository\ProblemConditionRepository;
use App\Model\Repository\ProblemRepository;
use App\Model\Repository\ProblemTemplateRepository;
use App\Model\Repository\TermRepository;
use App\Model\Repository\TestRepository;
use Dibi\UniqueConstraintViolationException;
use Nette\NotSupportedException;
use Nette\Utils\ArrayHash;

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
     * @var TestRepository
     */
    protected $testRepository;

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
     * @param TestRepository $testRepository
     * @param TestFunctionality $testFunctionality
     * @param GeneratorService $generatorService
     */
    public function __construct
    (
        TermRepository $termRepository, ProblemRepository $problemRepository,
        ProblemTemplateRepository $problemTemplateRepository, ProblemTemplateFunctionality $problemTemplateFunctionality,
        ProblemFinalFunctionality $problemFinalFunctionality, ProblemConditionRepository $problemConditionRepository,
        LogoRepository $logoRepository, LogoFunctionality $logoFunctionality,
        GroupRepository $groupRepository, TestRepository $testRepository, TestFunctionality $testFunctionality,
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
        $this->testRepository = $testRepository;
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
     * @param Test $test
     * @param string $variant
     * @param ArrayHash $data
     * @return void
     * @throws \Nette\Utils\JsonException
     */
    public function buildTestVariant(Test $test, string $variant, ArrayHash $data)
    {
        for($i = 0; $i < $data->problems_cnt; $i++){

            $problemTemplate = null;
            $problemId = $data['problem_'.$i];

            $problem = $this->problemRepository->find($problemId);

            //If the problem is prototype, it needs to be generated to it's final form
            if($problem->isTemplate()){

                $generatedFinal = $this->generatorService->generateWithConditions($problem);

                //Build final problem object
                $finalData = new ArrayHash();

                $finalData["text_before"] = $problem->getTextBefore();
                $finalData["body"] = $generatedFinal;
                $finalData["text_after"] = $problem->getTextAfter();
                $finalData["difficulty"] = $problem->getDifficulty()->getId();
                $finalData["type"] = $problem->getProblemType()->getId();
                $finalData["subcategory"] = $problem->getSubCategory()->getId();
                $finalData["problem_template_id"] = $problem->getId();
                $finalData["is_generated"] = true;

                if(method_exists($problem, "getFirstN"))
                    $finalData["first_n"] = $problem->getFirstN();
                if(method_exists($problem, "getVariable"))
                    $finalData["variable"] = $problem->getVariable();

                //Get prototype conditions
                $templateConditions = $problem->getConditions();

                //Store generated final problem to DB and switch problemId to it's ID
                $problemTemplate = $problem;

                $problemId = $this->problemFinalFunctionality->create($finalData, $templateConditions->getValues());
                $problem = $this->problemRepository->find($problemId);

            }

            //Attach current problem to the created test
            $this->testFunctionality->attachProblem($test, $problem, $variant, $problemTemplate ?? null, $data->{"newpage_" . $i});
        }

    }

    /**
     * @param ArrayHash $data
     * @return bool|ArrayHash
     * @throws \Nette\Utils\JsonException
     */
    public function buildTest(ArrayHash $data)
    {
        $variants = $this->testVariantsToArray($data);

        $testId = $this->testFunctionality->create(ArrayHash::from([
            "logo_id" => $data->logo_file_hidden,
            "term_id" => $data->test_term,
            "school_year" => $data->school_year,
            "test_number" => $data->test_number,
            "group_id" => $data->group,
            "introduction_text" => $data->introduction_text
        ]));

        $test = $this->testRepository->find($testId);

        foreach($variants as $variant){
            //Catch if there is one final problem more than once
            try{
                $this->buildTestVariant($test, $variant, $data);
            }
            catch(UniqueConstraintViolationException $e){
                throw new NotSupportedException('V testu se vyskytují dvě shodné úlohy.');
            }
        }

        $resArr = [
            "testId" => $testId,
            "variants" => $variants,
            "test" => $test
        ];

        return ArrayHash::from($resArr);
    }

}
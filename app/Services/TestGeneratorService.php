<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.3.19
 * Time: 9:46
 */

namespace App\Services;

use App\Exceptions\ProblemFinalCollisionException;
use App\Model\Entity\Test;
use App\Model\Functionality\ProblemFinalFunctionality;
use App\Model\Functionality\TestFunctionality;
use App\Model\Functionality\TestVariantFunctionality;
use App\Model\Repository\ProblemConditionTypeRepository;
use App\Model\Repository\ProblemRepository;
use App\Model\Repository\TestRepository;
use Nette\Utils\ArrayHash;

/**
 * Class TestGeneratorService
 * @package App\Services
 */
class TestGeneratorService
{
    /**
     * @var ProblemRepository
     */
    protected $problemRepository;

    /**
     * @var TestRepository
     */
    protected $testRepository;

    /**
     * @var ProblemConditionTypeRepository
     */
    protected $problemConditionTypeRepository;

    /**
     * @var ProblemFinalFunctionality
     */
    protected $problemFinalFunctionality;

    /**
     * @var TestFunctionality
     */
    protected $testFunctionality;

    /**
     * @var TestVariantFunctionality
     */
    protected $testVariantFunctionality;

    /**
     * @var GeneratorService
     */
    protected $generatorService;

    /**
     * @var array
     */
    protected $testVariantsLabels;

    /**
     * @var array
     */
    protected $problemConditionTypesId;

    /**
     * TestGeneratorService constructor.
     * @param ProblemRepository $problemRepository
     * @param TestRepository $testRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param ProblemFinalFunctionality $problemFinalFunctionality
     * @param TestFunctionality $testFunctionality
     * @param TestVariantFunctionality $testVariantFunctionality
     * @param GeneratorService $generatorService
     */
    public function __construct
    (
        ProblemRepository $problemRepository, TestRepository $testRepository,
        ProblemConditionTypeRepository $problemConditionTypeRepository,
        ProblemFinalFunctionality $problemFinalFunctionality, TestFunctionality $testFunctionality, TestVariantFunctionality $testVariantFunctionality,
        GeneratorService $generatorService
    )
    {
        $this->problemRepository = $problemRepository;
        $this->testRepository = $testRepository;
        $this->problemConditionTypeRepository = $problemConditionTypeRepository;
        $this->problemFinalFunctionality = $problemFinalFunctionality;
        $this->testFunctionality = $testFunctionality;
        $this->testVariantFunctionality = $testVariantFunctionality;
        $this->generatorService = $generatorService;

        $this->problemConditionTypesId = $this->problemConditionTypeRepository->findPairs([], 'id');

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
    protected function testVariantsToArray(ArrayHash $data): array
    {
        $variants = [];
        for($i = 0; $i < $data->variants; $i++){
            $variants[] = $this->testVariantsLabels[$i];
        }
        return $variants;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @return array
     */
    protected function getProblemFilters(int $id, ArrayHash $data): array
    {
        $filters['is_template'] = $data['is_template_' . $id];
        $filters['problem_type_id'] = $data['problem_type_id_' . $id];
        $filters['difficulty_id'] = $data['difficulty_id_' . $id];
        $filters['sub_category_id'] = $data['sub_category_id_' . $id];
        foreach ($this->problemConditionTypesId as $item){
            $filters['condition_type_id_' . $item] = $data['condition_type_id_' . $item . '_' . $id];
        }
        return $filters;
    }

    /**
     * @param array $arr
     * @return bool
     */
    protected function hasFree(array $arr): bool
    {
        foreach ($arr as $item){
            if($item){
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $keys
     * @param array $problems
     * @return array
     */
    protected function conjunctProblems(array $keys, array $problems): array
    {
        $res = [];
        foreach ($keys as $key){
            if(array_key_exists($key, $problems)){
                $res[] = $problems[$key];
            }
        }
        return $res;
    }

    /**
     * @param Test $test
     * @param string $variantLabel
     * @param ArrayHash $data
     * @return Test
     * @throws \Exception
     */
    protected function generateTestVariant(Test $test, string $variantLabel, ArrayHash $data): Test
    {
        // Create TestVariant entity
        $testVariant = $this->testVariantFunctionality->create(ArrayHash::from([
            'variantLabel' => $variantLabel,
            'test' => $test
        ]));

        //Array of chosen final problems IDs
        $usedFinals = [];

        for($i = 0; $i < $data->problems_cnt; $i++){
            $problemTemplate = null;
            $selectedProblems = $data['problem_' . $i];

            //In the case of random choice
            if(!$selectedProblems){

                // Get all problems that match filters
                $filters = $this->getProblemFilters($i, $data);
                $problems = $this->problemRepository->findFiltered($filters);

                // Get problem's templates that match filters
                $filters['is_template'] = 0;
                $finals = $this->problemRepository->findFiltered($filters);

                $finalsFree = [];
                foreach($finals as $final){
                    $finalsFree[$final->getId()] = true;
                }

                while(true){

                    // Generate index
                    $index = $this->generatorService->generateInteger(0, count($problems) - 1);

                    // Pick up the problem from problems array at generated index
                    $indexCounter = 0;
                    $problem = null;
                    foreach ($problems as $item){
                        if($indexCounter === $index){
                            $problem = $item;
                            break;
                        }
                        $indexCounter++;
                    }

                    // If there isn't any free final problem and all the problems are finals, stop and throw exception
                    if(!$this->hasFree($finalsFree) && (count($problems) === count($finals))){
                        throw new ProblemFinalCollisionException('Test nelze vygenerovat bez opakujících se úloh.');
                    }

                    // If the problems isn't template, mark used final problem
                    if(!$problem->isTemplate()){
                        if(!in_array($problem->getId(), $usedFinals, true)){
                            $usedFinals[] = $problem->getId();
                            break;
                        }
                        $finalsFree[$problem->getId()] = false;
                    }
                    else{
                        break;
                    }

                }
            }
            // If more problems was selected, pick one of them randomly
            else if(count($selectedProblems) > 1){

                // Get applied filters and extend in by not-template condition
                $filters = $this->getProblemFilters($i, $data);
                $filters['is_template'] = 0;

                // Get all final problem that match applied filters
                $finals = $this->problemRepository->findFiltered($filters);

                // Conjunct selected problems with filtered final problems
                $selectedFinals = $this->conjunctProblems($selectedProblems, $finals);

                // Prepare bool array for selected final problems
                $selectedFinalsFree = [];
                foreach ($selectedFinals as $free){
                    $selectedFinalsFree[$free->getId()] = true;
                }

                while(true){

                    // Generate index
                    $inx = $this->generatorService->generateInteger(0, count($selectedProblems) - 1);

                    // Pick up the problem from selected problems array
                    $problem = $this->problemRepository->find($selectedProblems[$inx]);

                    // If there isn't any free final problem and all the problems are finals, stop and throw exception
                    if(!$this->hasFree($selectedFinalsFree) && (count($selectedProblems) === count($selectedFinals))){
                        // TODO: It should be TestException!
                        throw new ProblemFinalCollisionException('Test nelze vygenerovat bez opakujících se úloh');
                    }

                    // If the problem isn't template, mak used final problem
                    if(!$problem->isTemplate()){
                        if(!in_array($problem->getId(), $usedFinals, true)){
                            $usedFinals[] = $problem->getId();
                            break;
                        }
                        $selectedFinalsFree[$problem->getId()] = false;
                    }
                    else{
                        break;
                    }

                }


            }
            // If only one problem was selected, just pick it up
            else{
                $problem = $this->problemRepository->find($selectedProblems[0]);
            }

            //If the problem is prototype, it needs to be generated to it's final form
            if($problem->isTemplate()){

                $generatedFinal = $this->generatorService->generateProblemFinal($problem);

                //Build final problem object
                $finalData = new ArrayHash();

                $finalData['textBefore'] = $problem->getTextBefore();
                $finalData['body'] = $generatedFinal;
                $finalData['textAfter'] = $problem->getTextAfter();
                $finalData['difficulty'] = $problem->getDifficulty()->getId();
                $finalData['problemFinalType'] = $problem->getProblemType()->getId();
                $finalData['subCategory'] = $problem->getSubCategory()->getId();
                $finalData['problem_template_id'] = $problem->getId();
                $finalData['is_generated'] = true;

                if(method_exists($problem, 'getFirstN')){
                    $finalData['first_n'] = $problem->getFirstN();
                }
                if(method_exists($problem, 'getVariable')){
                    $finalData['variable'] = $problem->getVariable();
                }

                //Get prototype conditions
                $templateConditions = $problem->getConditions();

                //Store generated final problem to DB and switch problemId to it's ID
                $problemTemplate = $problem;

                $problem = $this->problemFinalFunctionality->create($finalData, $templateConditions->getValues(), false);
            }
            else{
                $usedFinals[] = $problem->getId();
            }

            //Attach current problem to the created test
            $testVariant = $this->testVariantFunctionality->attachProblem($testVariant, $problem, $problemTemplate ?? null, $data->{'newpage_' . $i});
        }

        $test->addTestVariant($testVariant);

        return $test;
    }

    /**
     * @param ArrayHash $data
     * @return Test|Object|null
     * @throws \Exception
     */
    public function generateTest(ArrayHash $data)
    {
        $variants = $this->testVariantsToArray($data);
        $test = $this->testFunctionality->create(ArrayHash::from([
            'logo' => $data->logo,
            'term' => $data->testTerm,
            'schoolYear' => $data->schoolYear,
            'testNumber' => (int) $data->testNumber,
            'groups' => $data->groups,
            'introductionText' => $data->introductionText
        ]));
        if($test){
            foreach($variants as $variant){
                $test = $this->generateTestVariant($test, $variant, $data);
            }
        }
        return $test;
    }
}
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
use App\Model\Repository\ProblemRepository;
use App\Model\Repository\TestRepository;
use Nette\Utils\ArrayHash;

/**
 * Class TestBuilderService
 * @package App\Services
 */
class TestBuilderService
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
     * @var ProblemFinalFunctionality
     */
    protected $problemFinalFunctionality;

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
     * @param ProblemRepository $problemRepository
     * @param TestRepository $testRepository
     * @param ProblemFinalFunctionality $problemFinalFunctionality
     * @param TestFunctionality $testFunctionality
     * @param GeneratorService $generatorService
     */
    public function __construct
    (
        ProblemRepository $problemRepository, TestRepository $testRepository,
        ProblemFinalFunctionality $problemFinalFunctionality, TestFunctionality $testFunctionality,
        GeneratorService $generatorService
    )
    {
        $this->problemRepository = $problemRepository;
        $this->problemFinalFunctionality = $problemFinalFunctionality;
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
        if($data['is_template_' . $id] !== -1){
            $filters['is_template'] = $data['is_template_' . $id];
        }
        $filters['problem_type_id'] = $data['problem_type_id_' . $id];
        $filters['difficulty_id'] = $data['difficulty_id_' . $id];
        $filters['sub_category_id'] = $data['sub_category_id_' . $id];
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
     * @param Test $test
     * @param string $variant
     * @param ArrayHash $data
     * @return Test
     * @throws ProblemFinalCollisionException
     * @throws \Nette\Utils\JsonException
     */
    protected function buildTestVariant(Test $test, string $variant, ArrayHash $data): Test
    {
        //Array of chosen final problems IDs
        $usedFinals = [];

        for($i = 0; $i < $data->problems_cnt; $i++){
            $problemTemplate = null;
            $problemId = $data['problem_'.$i];

            //In the case of random choice
            if($problemId === 0){

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
                    $index = $this->generatorService->generateInteger(0, count($problems) - 1);
                    //var_dump($index);
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

                    //var_dump($problem);

                    if(!$this->hasFree($finalsFree) && (count($problems) === count($finals))){
                        throw new ProblemFinalCollisionException('Test nelze vygenerovat bez opakujících se úloh.');
                    }

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
            else{
                $problem = $this->problemRepository->find($problemId);
            }

            //If the problem is prototype, it needs to be generated to it's final form
            if($problem->isTemplate()){

                $generatedFinal = $this->generatorService->generateProblemFinal($problem);

                //Build final problem object
                $finalData = new ArrayHash();

                $finalData['text_before'] = $problem->getTextBefore();
                $finalData['body'] = $generatedFinal;
                $finalData['text_after'] = $problem->getTextAfter();
                $finalData['difficulty'] = $problem->getDifficulty()->getId();
                $finalData['type'] = $problem->getProblemType()->getId();
                $finalData['subcategory'] = $problem->getSubCategory()->getId();
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
            $test = $this->testFunctionality->attachProblem($test, $problem, $variant, $problemTemplate ?? null, $data->{'newpage_' . $i});
        }

        return $test;
    }

    /**
     * @param ArrayHash $data
     * @return bool|ArrayHash
     * @throws ProblemFinalCollisionException
     * @throws \Nette\Utils\JsonException
     * @throws \Exception
     */
    public function buildTest(ArrayHash $data)
    {
        $variants = $this->testVariantsToArray($data);

        $test = $this->testFunctionality->create(ArrayHash::from([
            'logo_id' => $data->logo_file_hidden,
            'term' => $data->test_term,
            'school_year' => $data->school_year,
            'test_number' => $data->test_number,
            'groups' => $data->groups,
            'introduction_text' => $data->introduction_text
        ]));

        foreach($variants as $variant){
            $test = $this->buildTestVariant($test, $variant, $data);
        }

        $resArr = [
            'testId' => $this->testRepository->getSequenceVal(),
            'variants' => $variants,
            'test' => $test
        ];

        return ArrayHash::from($resArr);
    }

}
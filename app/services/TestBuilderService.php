<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.3.19
 * Time: 9:46
 */

namespace App\Services;

use App\Model\Managers\ConditionManager;
use App\Model\Managers\GroupManager;
use App\Model\Managers\LogoManager;
use App\Model\Managers\ProblemFinalManager;
use App\Model\Managers\ProblemManager;

use App\Model\Managers\SpecializationManager;
use App\Model\Managers\TestManager;
use App\Model\Managers\TestTermManager;
use Dibi\Connection;
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
     * @var Connection
     */
    protected $db;

    /**
     * @var TestTermManager
     */
    protected $testTermManager;

    /**
     * @var ProblemManager
     */
    protected $problemManager;

    /**
     * @var ProblemFinalManager
     */
    protected $problemFinalManager;

    /**
     * @var ConditionManager
     */
    protected $conditionManager;

    /**
     * @var LogoManager
     */
    protected $logoManager;

    /**
     * @var GroupManager
     */
    protected $groupManager;

    /**
     * @var SpecializationManager
     */
    protected $specializationManager;

    /**
     * @var TestManager
     */
    protected $testManager;

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
     * @param Connection $connection
     * @param TestTermManager $testTermManager
     * @param ProblemManager $problemManager
     * @param ProblemFinalManager $problemFinalManager
     * @param ConditionManager $conditionManager
     * @param LogoManager $logoManager
     * @param GroupManager $groupManager
     * @param SpecializationManager $specializationManager
     * @param TestManager $testManager
     * @param GeneratorService $generatorService
     */
    public function __construct
    (
        Connection $connection,
        TestTermManager $testTermManager, ProblemManager $problemManager, ProblemFinalManager $problemFinalManager,
        ConditionManager $conditionManager, LogoManager $logoManager,
        GroupManager $groupManager, SpecializationManager $specializationManager, TestManager $testManager,
        GeneratorService $generatorService
    )
    {
        $this->db = $connection;
        $this->testTermManager = $testTermManager;
        $this->problemManager = $problemManager;
        $this->problemFinalManager = $problemFinalManager;
        $this->conditionManager = $conditionManager;
        $this->logoManager = $logoManager;
        $this->groupManager = $groupManager;
        $this->specializationManager = $specializationManager;
        $this->testManager = $testManager;
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
     * @param iterable $filters
     * @return array
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function filterProblems(iterable $filters)
    {

        //Parse filters array and build filter condition

        $resArr = [];

        //bdump($filters);

        foreach($filters as $problemKey => $problemFilters){

                bdump($problemKey);
                bdump($problemFilters);

                $cond = '';

                if(isset($problemFilters['filters'])) {

                    $arrayKeys = array_keys($problemFilters['filters']);

                    $lastKey = end($arrayKeys);

                    foreach ($problemFilters['filters'] as $filterType => $filterVal) {
                        if (($filterType != 'is_prototype' || $filterVal != -1) && ($filterType == 'is_prototype' || $filterVal != 0)) {
                            if ($filterType == $lastKey)
                                $cond .= $filterType . ' = ' . $filterVal;
                            else
                                $cond .= $filterType . ' = ' . $filterVal . ' AND ';
                        }
                    }

                }
                //Trim AND from query end
                if( Strings::endsWith($cond, ' AND ') )
                    $cond = Strings::substring($cond, 0, Strings::length($cond) - 5);

                bdump($cond);

                $cond ? $res = $this->problemManager->getByCond($cond,"ASC", true) : $res = $this->problemManager->getAll('DESC', true);

                $resArr[$problemKey] = $res;

        }

        return $resArr;

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
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function getLogoData(ArrayHash $data)
    {
        $logoId = $data->logo_file_hidden;

        $logo = $this->logoManager->getById($logoId);
        $logoPath = './' . Strings::substring($logo->path, Strings::indexOf($logo->path, DIRECTORY_SEPARATOR, -1)+1);

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
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function buildVariantHead(string $variant, ArrayHash $data, string $logoPath = null)
    {
        $testHeader = [];

        $testHeader["variant"] = $variant;
        if($logoPath)
            $testHeader["logo"] = $logoPath;
        $testHeader["group"] = $this->groupManager->getById($data->group)->label;
        $testHeader["test_term"] = $this->testTermManager->getById($data->test_term)->label;
        $testHeader["school_year"] = $data->school_year;
        $testHeader["test_number"] = $data->test_number;

        return $testHeader;
    }

    /**
     * @param int $testId
     * @param string $variant
     * @param ArrayHash $data
     * @return mixed
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     * @throws \Nette\Utils\JsonException
     */
    public function buildVariantBody(int $testId, string $variant, ArrayHash $data)
    {
        $body = [];
        $body["introduction_text"] = $data->introduction_text;
        for($i = 0; $i < $data->problems_cnt; $i++){

            $problemPrototypeId = null;
            $problemId = $data['problem_'.$i];
            $problem = $this->problemManager->getById($problemId);

            $body['problems'][$i]['before'] = $problem->text_before;

            //If the problem is prototype, it needs to be generated to it's final form
            if($problem->is_prototype){

                $generatedFinal = $this->generatorService->generateWithConditions($problem);

                //Build final problem object
                $prototypeData = new ArrayHash();
                $prototypeData["before"] = $problem->text_before;
                $prototypeData["structure"] = $generatedFinal;
                $prototypeData["after"] = $problem->text_after;
                $prototypeData["difficulty"] = $problem->difficulty_id;
                $prototypeData["type"] = $problem->problem_type_id;
                $prototypeData["result"] = "";
                $prototypeData["subcategory"] = $problem->sub_category_id;
                $prototypeData["first_n"] = $problem->first_n;
                $prototypeData["variable"] = $problem->variable;

                //Get prototype conditions
                $prototypeConditions = $this->conditionManager->getByProblem($problemId);

                //Store generated final problem to DB and switch problemId to it's ID
                $problemPrototypeId = $problemId;
                $problemId = $this->problemFinalManager->createFinal($prototypeData, $prototypeConditions, true, true);
                $this->problemManager->update($problemPrototypeId, ['is_used' => true]);

                $body['problems'][$i]['structure'] = $generatedFinal;

            }
            else{
                $body['problems'][$i]['structure'] = $problem->structure;
            }

            $body['problems'][$i]['after'] = $problem->text_after;
            $body["problems"][$i]["newpage"] = $data->{"newpage_" . $i};

            //Attach current problem to the created test
            $this->testManager->attachProblem($testId, $variant, $problemId, $problemPrototypeId ?? null, $data->{"newpage_" . $i});
        }

        return $body;
    }

    /**
     * @param int $testId
     * @param string $variant
     * @param ArrayHash $data
     * @param $logoData
     * @return array
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
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
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     * @throws \Nette\Utils\JsonException
     */
    public function buildTest(ArrayHash $data)
    {
        $test = [];

        $variants = $this->testVariantsToArray($data);
        $logoData = $this->getLogoData($data);

        bdump($variants);

        $this->db->begin();

        $testId = $this->testManager->create([
            "logo_id" => $logoData->logoId,
            "test_term_id" => $data->test_term,
            "school_year" => $data->school_year,
            "test_number" => $data->test_number,
            "group_id" => $data->group
        ]);

        $this->logoManager->update($logoData->logoId,[
            "is_used" => true
        ]);

        foreach($variants as $variant){
            //Catch if there is one final problem more than once
            try{
                $test[$variant] = $this->buildTestVariant($testId, $variant, $data, $logoData);
            }
            catch(UniqueConstraintViolationException $e){
                $this->db->rollback();
                throw new NotSupportedException('V testu se vyskytují dvě shodné úlohy.');
            }
        }

        bdump($test);

        $this->db->commit();

        $resArr = [
            "testId" => $testId,
            "test" => $test
        ];

        return ArrayHash::from($resArr);
    }

}
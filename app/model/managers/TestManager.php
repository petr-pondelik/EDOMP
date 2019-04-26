<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 16.3.19
 * Time: 19:58
 */

namespace App\Model\Managers;

use App\Helpers\ConstHelper;
use App\Model\Entities\Test;
use App\Services\FileService;
use Dibi\Connection;
use Dibi\Exception;
use Nette\Utils\FileSystem;

/**
 * Class TestManager
 * @package app\model\managers
 */
class TestManager extends BaseManager
{

    /**
     * @var string
     */
    protected $table = 'test';

    /**
     * @var string
     */
    protected $problemRelTable = 'problem_test_rel';

    /**
     * @var string
     */
    protected $rowClass = Test::class;

    /**
     * @var string
     */
    protected $labelCol = 'test_id';

    /**
     * @var array
     */
    protected $selectColumns = [
        'test_id',
        'created',
        'logo_id',
        'group_id'
    ];

    /**
     * @var ProblemManager
     */
    protected $problemManager;

    /**
     * @var LogoManager
     */
    protected $logoManager;

    /**
     * TestManager constructor.
     * @param ProblemManager $problemManager
     * @param LogoManager $logoManager
     * @param Connection $connection
     * @param string|null $table
     */
    public function __construct
    (
        ProblemManager $problemManager, LogoManager $logoManager,
        ConstHelper $constHelper,
        Connection $connection, string $table = null
    )
    {
        parent::__construct($constHelper, $connection, $table);
        $this->problemManager = $problemManager;
        $this->logoManager = $logoManager;
    }

    /**
     * @param int $testId
     * @param string $variant
     * @param int $problemFinalId
     * @param int|null $problemPrototypeId
     * @param bool $newpage
     * @return \Dibi\Result|int
     * @throws Exception
     */
    public function attachProblem(int $testId, string $variant, int $problemFinalId, int $problemPrototypeId = null, bool $newpage = false)
    {
        //Attach problem to test via problem_test_rel table
        return $this->db->insert($this->problemRelTable, [
            "test_id" => $testId,
            "variant" => $variant,
            "problem_final_id" => $problemFinalId,
            "problem_prototype_id" => $problemPrototypeId,
            "newpage" => $newpage
        ])->execute();
    }

    /**
     * @param int $testId
     * @return array
     */
    public function getVariants(int $testId)
    {
        return $this->db->select('variant')
            ->from($this->table)
            ->join('problem_test_rel')
            ->where($this->table . '.' . $this->getPrimary() . ' = problem_test_rel ' . '.' . $this->getPrimary() . ' AND ' . $this->table . '.' . $this->getPrimary() . ' = ' .$testId )
            ->groupBy('variant')
            ->fetchAll();
    }

    /**
     * @param int $testId
     * @return \Dibi\Result|int|void
     * @throws \Dibi\Exception
     */
    public function delete(int $testId)
    {
        //Look for the attached problems, remove test from DB, check if the attached problems are still in usage. If not, then set their usage flag to false

        $detachedProblems = $this->problemManager->getByTestId($testId);

        $logoId = $this->getById($testId)->logo_id;

        parent::delete($testId);

        if(!$this->logoManager->isInUsage((int) $logoId)){
            $this->logoManager->update((int) $logoId, [
                "is_used" => false
            ]);
        }

        foreach($detachedProblems as $detachedProblem){
            if(!$this->problemManager->isInUsage($detachedProblem->problem_final_id)){
                $this->problemManager->update($detachedProblem->problem_final_id, [
                    "is_used" => false
                ]);
            }
        }

        FileSystem::delete(DATA_DIR . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . $testId);
    }

    public function getProblems(int $testId)
    {
        return $this->db->select("problem_final_id, problem_prototype_id, structure, problem_test_rel.success_rate")
            ->from($this->table)
            ->join($this->problemRelTable)
            ->using("(test_id)")
            ->join("problem")
            ->on("problem.problem_id = " . $this->problemRelTable . ".problem_final_id")
            ->where("test_id = ?", $testId)
            ->fetchAssoc("problem_final_id");
    }

    public function updateSuccessRate(int $problemId, float $successRate)
    {
        return $this->db->update($this->problemRelTable, [
            "success_rate" => $successRate
        ])
            ->where("problem_final_id = " . $problemId)
            ->execute();
    }

}
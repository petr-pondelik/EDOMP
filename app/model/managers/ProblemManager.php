<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.2.19
 * Time: 21:31
 */

namespace App\Model\Managers;

use App\Helpers\ConstHelper;
use App\Model\Entities\Problem;
use Dibi\Connection;
use Dibi\NotSupportedException;

use Nette\Utils\Json;
use Tracy\Debugger;

/**
 * Class ProblemManager
 * @package App\Model
 */
class ProblemManager extends BaseManager
{
    /**
     * @var string
     */
    protected $table = 'problem';

    /**
     * @var string
     */
    protected $finalTable = 'problem_final';

    /**
     * @var string
     */
    protected $prototypeTable = 'problem_prototype';

    /**
     * @var string
     */
    protected $testRelTable = 'problem_test_rel';

    /**
     * @var string
     */
    protected $rowClass = Problem::class;

    /**
     * @var string
     */
    protected $labelCol = 'problem_id';

    /**
     * @var array
     */
    protected $selectColumns = [
        'problem.problem_id',
        'text_before',
        'structure',
        'text_after',
        'created',
        'difficulty_id',
        'problem_type_id',
        'is_prototype',
        'is_used',
        "sub_category_id",
        "success_rate",
        "variable",
        "first_n"
    ];

    /**
     * @param int $testId
     * @return array
     * @throws \Dibi\Exception
     */
    public function getByTestId(int $testId)
    {
        return $this->db->select("problem_final_id")
            ->from($this->testRelTable)
            ->where("test_id = " . $testId)
            ->execute()
            ->fetchAll();
    }

    /**
     * @param int $problemId
     * @return bool
     * @throws \Dibi\Exception
     */
    public function isInUsage(int $problemId): bool
    {
        $res = $this->db->select("*")
            ->from($this->testRelTable)
            ->where("problem_final_id = " . $problemId)
            ->execute()
            ->fetch();
        return $res ? true : false;
    }

    public function calculateSuccessRate(int $problemId, bool $prototype = false)
    {
        $rows = $this->db->select("success_rate")
            ->from($this->testRelTable)
            ->where(!$prototype ? "problem_final_id = " . $problemId : "problem_prototype_id = " . $problemId)
            ->fetchAll();

        $cnt = 0;
        $ratingsSum = 0;

        foreach ($rows as $row){
            if(!empty($row->success_rate)){
                $cnt++;
                $ratingsSum += $row->success_rate;
            }
        }

        if($cnt > 0){
            $this->update($problemId, [
                "success_rate" => ($ratingsSum/$cnt)
            ]);
        }
    }
}
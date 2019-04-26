<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.3.19
 * Time: 13:11
 */

namespace App\Model\Managers;

use App\Model\Entities\Condition;

/**
 * Class ConditionManager
 * @package App\Model\Managers
 */
class ConditionManager extends BaseManager
{
    /**
     * @var string
     */
    protected $table = 'condition';

    /**
     * @var string
     */
    protected $rowClass = Condition::class;

    /**
     * @var array
     */
    protected $selectColumns = [
        'condition_id',
        'accessor',
        'label'
    ];

    /**
     * @var string
     */
    protected $labelCol = 'label';

    /**
     * @param int $condTypeId
     * @return array[]|\Dibi\Row[]
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function getByCondType(int $condTypeId)
    {
        return $this->getSelect('ASC')
            ->where('condition_type_id = ?', $condTypeId)
            ->execute()
            ->setRowClass($this->rowClass)
            ->fetchAll();
    }

    /**
     * @param int $problemTypeId
     * @return array[]|\Dibi\Row[]
     * @throws \Dibi\Exception
     */
    public function getByProblemType(int $problemTypeId)
    {
        return $this->db->select('condition.condition_id, condition.label, condition.accessor, condition.condition_type_id')
            ->from('problem_tp_condition_tp_rel')
            ->join('condition_type')
            ->on('problem_tp_condition_tp_rel.condition_type_id = condition_type.condition_type_id')
            ->join('condition')
            ->on('condition_type.condition_type_id = condition.condition_type_id')
            ->where('problem_type_id = ?', $problemTypeId)
            ->execute()
            ->setRowClass($this->rowClass)
            ->fetchAll();
    }

    /**
     * @param int $accessor
     * @param int $condTypeId
     * @return \Dibi\Row|false
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function getByAccessor(int $accessor, int $condTypeId)
    {
        return $this->getSelect()
                ->where("condition_type_id = ? AND accessor = ?", $condTypeId, $accessor)
                ->execute()
                ->setRowClass($this->rowClass)
                ->fetch();
    }

    /**
     * @param int $problemId
     * @return \Dibi\Result|int
     * @throws \Dibi\Exception
     */
    public function deleteByProblem(int $problemId)
    {
        return $this->db->delete('condition_problem_rel')
                ->where('problem_id = ?', $problemId)
                ->execute();
    }

    /**
     * @param int $problemId
     * @return Condition[]|\Dibi\Row[]
     * @throws \Dibi\Exception
     */
    public function getByProblem(int $problemId)
    {
        return $this->db->select('condition.condition_id, condition.label, condition.accessor, condition.condition_type_id')
                ->from('condition_problem_rel')
                ->join('condition')
                ->on('condition_problem_rel.condition_id = condition.condition_id')
                ->join('condition_type')
                ->on('condition.condition_type_id = condition_type.condition_type_id')
                ->where('problem_id = ?', $problemId)
                ->execute()
                ->setRowClass($this->rowClass)
                ->fetchAll();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.2.19
 * Time: 15:59
 */

namespace App\Model\Managers;

/**
 * Class ConditionTypeManager
 * @package App\Model\Managers
 */
class ConditionTypeManager extends BaseManager
{
    /**
     * @var string
     */
    protected $table = 'condition_type';

    /**
     * @var string
     */
    protected $problemTpRelTable = 'problem_tp_condition_tp_rel';

    /**
     * @var string
     */
    protected $labelCol = 'accessor';

    /**
     * @var array
     */
    protected $selectColumns = [
        'condition_type.condition_type_id',
        'accessor'
    ];

    /**
     * @param int $problemTypeId
     * @return array[]|\Dibi\Row[]
     * @throws \Dibi\Exception
     */
    public function getByProblemType(int $problemTypeId)
    {
        return $this->db->select($this->selectColumns)
                ->from($this->table)->join($this->problemTpRelTable)
                ->on('condition_type.condition_type_id = problem_tp_condition_tp_rel.condition_type_id')
                ->where('problem_tp_condition_tp_rel.problem_type_id = '.$problemTypeId)
                ->execute()
                ->fetchAll();
    }

    /**
     * @param string $accessor
     * @return \Dibi\Row|false
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function getByAccessor(string $accessor)
    {
        return $this->getSelect()
                ->where("accessor = '".$accessor."'")
                ->execute()
                ->fetch();
    }
}
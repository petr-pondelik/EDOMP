<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.4.19
 * Time: 14:15
 */

namespace App\Model\Managers;

use App\Helpers\ConstHelper;
use App\Helpers\FormatterHelper;
use App\Model\Entities\Condition;
use App\Model\Entities\ProblemFinal;
use App\Model\Entities\ProblemType;
use Dibi\Connection;
use Dibi\NotSupportedException;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemFinalManager
 * @package App\Model\Managers
 */
class ProblemFinalManager extends BaseManager
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
    protected $rowClass = ProblemFinal::class;

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
        'is_generatable',
        'result',
        'sub_category_id',
        "success_rate",
        "variable",
        "first_n"
    ];

    /**
     * @var ProblemTypeManager
     */
    protected $problemTypeManager;

    /**
     * @var ConditionManager
     */
    protected $conditionManager;

    /**
     * @var ConditionTypeManager
     */
    protected $conditionTypeManager;

    /**
     * @var FormatterHelper
     */
    protected $formatterHelper;

    /**
     * ProblemFinalManager constructor.
     * @param ProblemTypeManager $problemTypeManager
     * @param ConditionManager $conditionManager
     * @param ConditionTypeManager $conditionTypeManager
     * @param ConstHelper $constHelper
     * @param FormatterHelper $formatterHelper
     * @param Connection $connection
     * @param string|null $table
     */
    public function __construct
    (
        ProblemTypeManager $problemTypeManager, ConditionManager $conditionManager, ConditionTypeManager $conditionTypeManager,
        ConstHelper $constHelper, FormatterHelper $formatterHelper,
        Connection $connection, string $table = null
    )
    {
        parent::__construct($constHelper, $connection, $table);
        $this->problemTypeManager = $problemTypeManager;
        $this->conditionManager = $conditionManager;
        $this->conditionTypeManager = $conditionTypeManager;
        $this->formatterHelper = $formatterHelper;
    }

    /**
     * @param string $order
     * @param null $select
     * @param mixed ...$args
     * @return \Dibi\Fluent
     * @throws NotSupportedException
     */
    public function getSelect(string $order = 'DESC', $select = NULL, ...$args)
    {
        if(empty($this->selectColumns)){
            throw new NotSupportedException('Empty select column list. Did you specified [selectColumns] attribute?');
        }
        else{
            return $this->db->select($select !== NULL ? array_merge($this->selectColumns, (array)$select) : $this->selectColumns, ...$args)
                ->from($this->table)->join($this->finalTable)
                ->on('problem.problem_id = problem_final.problem_id')
                ->where('is_prototype = false')
                ->orderBy('problem.problem_id', 'DESC');
        }
    }

    /**
     * @param int $id
     * @param null $select
     * @param mixed ...$args
     * @return \Dibi\Row|false
     * @throws \Dibi\Exception
     */
    public function getFinalById(int $id, $select = NULL, ...$args)
    {
        return $this->db->select($select !== NULL ? array_merge($this->selectColumns, (array)$select) : $this->selectColumns, ...$args)
            ->from($this->table)
            ->join($this->finalTable)
            ->on('problem.problem_id = problem_final.problem_id')
            ->where('problem.problem_id = ?', $id)
            ->execute()
            ->setRowClass($this->rowClass)
            ->fetch();
    }

    /**
     * @param $condition
     * @param int $problemId
     * @return \Dibi\Result|int
     * @throws \Dibi\Exception
     */
    public function attachCondition($condition, int $problemId)
    {
        return $this->db->insert('condition_problem_rel', [
            'condition_id' => $condition->condition_id,
            'problem_id' => $problemId
        ])
            ->execute();
    }

    /**
     * @param int $problemId
     * @return \Dibi\Result|int
     * @throws \Dibi\Exception
     */
    public function detachConditions(int $problemId)
    {
        return $this->db->delete('condition_problem_rel')
            ->where('problem_id = ?', $problemId)
            ->execute();
    }

    /**
     * @param iterable $data
     * @param array|null $conditions
     * @param bool $isUsed
     * @return int
     * @throws NotSupportedException
     * @throws \Dibi\Exception
     */
    public function createFinal(iterable $data, array $conditions = null, bool $isUsed = false, bool $fromPrototype = false)
    {
        $type = $this->problemTypeManager->getById($data->type);

        $firstN = null;
        if($data->type === $this->constHelper::ARITHMETIC_SEQ)
            $firstN = $data->first_n_arithmetic_seq;
        else if($data->type === $this->constHelper::GEOMETRIC_SEQ)
            $firstN = $data->first_n_geometric_seq;

        $problemData = [
            "text_before" => $data->before,
            "structure" => $data->structure,
            "text_after" => $data->after,
            "difficulty_id" => $data->difficulty,
            "problem_type_id" => $data->type,
            "is_used" => $isUsed,
            "is_generatable" => $type->is_generatable && $fromPrototype ? true : false,
            "first_n" => $firstN,
            "sub_category_id" => $data->subcategory,
            "variable" => $data->variable ?? null
        ];

        $problemId = $this->create($problemData);

        $finalData = [
            "problem_id" => $problemId,
            "result" => $data->result
        ];

        $this->db->insert($this->finalTable, $finalData)->execute();

        if(!$conditions){

            //Connect created final problem with entered conditions

            $problemCondTypes = $this->conditionTypeManager->getByProblemType($data->type);

            foreach($problemCondTypes as $problemCondType){

                //Get ConditionType ID by accessor
                $condTypeId = $problemCondType->condition_type_id;

                //Get ConditionType value from created problem
                $condTypeVal = $data->{'condition_' . $condTypeId};

                //Connect problem with condition
                $condition = $this->conditionManager->getByAccessor($condTypeVal, $condTypeId);

                $this->attachCondition($condition, $problemId);
            }

        }
        else{
            //Just attach entered conditions
            foreach($conditions as $condition)
                $this->attachCondition($condition, $problemId);
        }

        return $problemId;
    }

    /**
     * @param $id
     * @param iterable $problemData
     * @param iterable $finalData
     * @throws \Dibi\Exception
     */
    public function updateFinalInline(int $id, iterable $problemData, iterable $finalData)
    {
        $this->update($id, $problemData);
        $this->db->update($this->finalTable, $finalData)
            ->where('problem_id = ?', $id)
            ->execute();
    }

    /**
     * @param int $problemId
     * @param iterable $problemData
     * @param iterable $finalData
     * @param iterable $conditionData
     * @throws NotSupportedException
     * @throws \Dibi\Exception
     */
    public function updateFinal(int $problemId, iterable $problemData, iterable $finalData, iterable $conditionData)
    {
        $this->updateFinalInline($problemId, $problemData, $finalData);

        bdump($problemData);

        $problemCondTypes = $this->conditionTypeManager->getByProblemType($problemData['problem_type_id']);

        bdump($problemCondTypes);

        $this->detachConditions($problemId);

        foreach($problemCondTypes as $problemCondType){

            //Get ConditionType ID
            $condTypeId = $problemCondType->condition_type_id;

            //Get ConditionType value from created problem
            $condTypeVal = $conditionData[$condTypeId];

            //Connect problem with condition
            $condition = $this->conditionManager->getByAccessor($condTypeVal, $condTypeId);

            $this->attachCondition($condition, $problemId);
        }
    }

    /**
     * @param int $id
     * @param ArrayHash $resultArray
     * @return \Dibi\Result|int
     * @throws \Dibi\Exception
     */
    public function storeResult(int $id, ArrayHash $resultArray)
    {
        $result = $this->formatterHelper->formatResult($resultArray);
        return $this->db->update($this->finalTable, [
            "result" => $result
        ])->where($this->getPrimary().' = ?', $id)->execute();
    }
}
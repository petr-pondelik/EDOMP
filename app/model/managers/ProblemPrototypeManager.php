<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.4.19
 * Time: 14:16
 */

namespace App\Model\Managers;

use App\Helpers\ConstHelper;
use App\Model\Entities\Problem;
use Dibi\Connection;
use Dibi\NotSupportedException;
use Nette\Utils\Json;

/**
 * Class ProblemPrototypeManager
 * @package App\Model\Managers
 */
class ProblemPrototypeManager extends BaseManager
{
    /**
     * @var string
     */
    protected $table = 'problem';

    /**
     * @var string
     */
    protected $prototypeTable = 'problem_prototype';

    /**
     * @var string
     */
    protected $rowClass = Problem::class;

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
        'sub_category_id',
        "success_rate",
        "variable",
        "first_n"
    ];

    /**
     * @var PrototypeJsonDataManager
     */
    protected $prototypeJsonDataManager;

    /**
     * @var ConditionManager
     */
    protected $conditionManager;

    /**
     * @var ConditionTypeManager
     */
    protected $conditionTypeManager;

    /**
     * ProblemPrototypeManager constructor.
     * @param ConditionManager $conditionManager
     * @param ConditionTypeManager $conditionTypeManager
     * @param PrototypeJsonDataManager $prototypeJsonDataManager
     * @param ConstHelper $constHelper
     * @param Connection $connection
     * @param string|null $table
     */
    public function __construct(
        ConditionManager $conditionManager, ConditionTypeManager $conditionTypeManager,
        PrototypeJsonDataManager $prototypeJsonDataManager,
        ConstHelper $constHelper,
        Connection $connection, string $table = null
    )
    {
        parent::__construct($constHelper, $connection, $table);
        $this->conditionManager = $conditionManager;
        $this->conditionTypeManager = $conditionTypeManager;
        $this->prototypeJsonDataManager = $prototypeJsonDataManager;
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
                ->from($this->table)->where('is_prototype = true');
        }
    }

    /**
     * @param int $id
     * @param string $select
     * @param mixed ...$args
     * @return \Dibi\Row|false
     */
    public function getPrototypeById(int $id, $select = 'matches', ...$args)
    {
        return $this->db->select($select !== NULL ? array_merge($this->selectColumns, (array)$select) : $this->selectColumns, ...$args)
            ->from($this->table)
            ->join($this->prototypeTable)
            ->on('problem.problem_id = problem_prototype.problem_id')
            ->where('problem.problem_id = ?', $id)
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
     * @throws NotSupportedException
     * @throws \Dibi\Exception
     * @throws \Nette\Utils\JsonException
     */
    public function createPrototype(iterable $data)
    {
        bdump($data);

        $firstN = null;
        if($data->type === $this->constHelper::ARITHMETIC_SEQ)
            $firstN = $data->first_n_arithmetic_seq;
        else
            $firstN = $data->first_n_geometric_seq;

        $problemData = [
            "text_before" => $data->before,
            "structure" => $data->structure,
            "text_after" => $data->after,
            "difficulty_id" => $data->difficulty,
            "problem_type_id" => $data->type,
            "sub_category_id" => $data->subcategory,
            "variable" => $data->variable,
            "first_n" => $firstN,
            'is_prototype' => true
        ];

        $problemId = $this->create($problemData);

        //Store parameters that matches prototype conditions into problem_prototype

        $prototypeJsonData = $this->prototypeJsonDataManager->getByCond('problem_id = ' . $problemId);
        if($prototypeJsonData)
            $prototypeJsonData = Json::decode($prototypeJsonData);
        else
            $prototypeJsonData = null;

        $prototypeData = [
            'problem_id' => $problemId,
            'matches' => $prototypeJsonData ? Json::encode($prototypeJsonData->matches) : $prototypeJsonData
        ];

        $this->db->insert($this->prototypeTable, $prototypeData)->execute();

        //Connect created problem with entered conditions

        $problemCondTypes = $this->conditionTypeManager->getByProblemType($data->type);

        foreach($problemCondTypes as $problemCondType){

            //Get ConditionType ID
            $condTypeId = $problemCondType->condition_type_id;

            //Get ConditionType value from created problem
            $condTypeVal = $data->{'condition_' . $condTypeId};

            //Connect problem with condition
            $condition = $this->conditionManager->getByAccessor($condTypeVal, $condTypeId);

            $this->attachCondition($condition, $problemId);
        }
    }

    /**
     * @param int $problemId
     * @param iterable $problemData
     * @param iterable $conditionData
     * @throws NotSupportedException
     * @throws \Dibi\Exception
     * @throws \Nette\Utils\JsonException
     */
    public function updatePrototype(int $problemId, iterable $problemData, iterable $conditionData = [])
    {
        $this->update($problemId, $problemData);

        $problemCondTypes = $this->conditionTypeManager->getByProblemType($problemData['problem_type_id']);

        $this->detachConditions($problemId);

        //Store parameters that matches prototype conditions into problem_prototype

        $prototypeJsonData = $this->prototypeJsonDataManager->getByCond('problem_id = ' . $problemId);

        if($prototypeJsonData)
            $prototypeJsonData = Json::decode($prototypeJsonData);
        else
            $prototypeJsonData = null;

        $prototypeData = [
            'matches' => $prototypeJsonData ? Json::encode($prototypeJsonData->matches) : $prototypeJsonData
        ];

        $this->db->update($this->prototypeTable, $prototypeData)
            ->where('problem_id = ?', $problemId)
            ->execute();

        foreach($problemCondTypes as $problemCondType){

            //Get ConditionType ID by accessor
            $condTypeId = $problemCondType->condition_type_id;

            //Get ConditionType value from created problem
            $condTypeVal = $conditionData[$condTypeId];

            //Connect problem with condition
            $condition = $this->conditionManager->getByAccessor($condTypeVal, $condTypeId);

            if(!$condition->accessor)
                $this->db->update($this->prototypeTable, [
                    "matches" => null
                ])
                    ->where('problem_id = ?', $problemId)
                    ->execute();

            $this->attachCondition($condition, $problemId);
        }
    }

    /**
     * @param int $id
     * @param int $newType
     * @return bool
     * @throws \Dibi\Exception
     */
    public function typeChange(int $id, int $newType)
    {
        $oldType = $this->db->select('problem_type_id')
            ->from($this->table)
            ->where('problem_id = ?', $id)
            ->execute()
            ->fetchSingle();
        return ($oldType !== $newType);
    }
}
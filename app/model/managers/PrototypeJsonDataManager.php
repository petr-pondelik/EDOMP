<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 2.4.19
 * Time: 0:13
 */

namespace App\Model\Managers;

use App\Model\Entities\PrototypeJsonData;
use Dibi\UniqueConstraintViolationException;

/**
 * Class PrototypeJsonDataManager
 * @package app\model\managers
 */
class PrototypeJsonDataManager extends BaseManager
{
    /**
     * @var string
     */
    protected $table = 'prototype_json_data';

    /**
     * @var string
     */
    protected $rowClass = PrototypeJsonData::class;

    /**
     * @var array
     */
    protected $selectColumns = [
        'prototype_json_data_id',
        'json_data'
    ];

    /**
     * @param string $cond
     * @param string $order
     * @param bool $empty
     * @return \Dibi\Row|false
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function getByCond(string $cond, string $order = "ASC", bool $empty = false)
    {
        return $this->getSelect()
            ->where($cond)
            ->orderBy($this->getPrimary() . " " . $order)
            ->execute()
            ->setRowClass($this->rowClass)
            ->fetch();
    }

    /**
     * @param string $data
     * @param int|null $problemId
     * @return void
     * @throws \Dibi\Exception
     */
    public function storePrototypeJsonData(string $data, int $problemId = null)
    {

        if(!$problemId){
            $problemId = $this->db->select('AUTO_INCREMENT')
                ->from('INFORMATION_SCHEMA.TABLES')
                ->where('TABLE_SCHEMA = ? AND TABLE_NAME = ?', 'edomp', 'problem')
                ->fetchSingle();
        }
        bdump($problemId);

        /*$found = $this->db->select('prototype_json_data_id')
            ->from($this->table)
            ->where('problem_id = ?', $problemId)
            ->execute()
            ->fetch();

        bdump($found);*/

            $this->db->begin();

            try{
                $this->db->query("INSERT INTO $this->table %v", [
                    'json_data' => $data,
                    'problem_id' => $problemId
                ]);
            } catch(UniqueConstraintViolationException $e) {
                $this->db->rollback();

                $this->db->update($this->table, [
                        'json_data' => $data
                    ])
                    ->where('problem_id = ?', $problemId)
                    ->execute();

                return;
            }

            $this->db->commit();

    }
}
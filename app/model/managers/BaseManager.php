<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.2.19
 * Time: 21:37
 */

namespace App\Model\Managers;

use App\Helpers\ConstHelper;
use App\Model\Entities\BaseEntity;
use Dibi\Exception;
use Nette\SmartObject;

use Dibi\Connection;
use Dibi\NotSupportedException;

/**
 * Class BaseManager
 */
abstract class BaseManager
{

    use SmartObject;

    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $rowClass = BaseEntity::class;

    /**
     * @var string
     */
    protected $labelCol = '';

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * BaseManager constructor.
     * @param ConstHelper $constHelper
     * @param Connection $connection
     * @param string $table
     */
    public function __construct
    (
        ConstHelper $constHelper,
        Connection $connection, string $table = null
    )
    {
        $this->db = $connection;
        $this->table = $this->table ?? $table;
        $this->constHelper = $constHelper;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getPrimary(): string
    {
        return $this->table.'_id';
    }

    /**
     * @param string $order
     * @param null $select
     * @param array ...$args
     * @return \Dibi\Fluent
     * @throws NotSupportedException
     */
    public function getSelect(string $order = 'DESC', $select = null, ...$args)
    {
        if(empty($this->selectColumns)){
            throw new NotSupportedException('Empty select column list. Did you specified [selectColumns] attribute?');
        }
        else{
            return $this->db->select($select !== null ? array_merge($this->selectColumns, (array)$select) : $this->selectColumns, ...$args)
                ->from($this->table)->orderBy($this->getPrimary().' '.$order);
        }
    }

    /**
     * @param string $order
     * @return array
     * @throws Exception
     * @throws NotSupportedException
     */
    public function getAll(string $order = 'DESC')
    {
        return $this->getSelect($order)
            ->execute()
            ->setRowClass($this->rowClass)
            ->fetchAssoc($this->getPrimary());
    }

    public function getAllPairs(string $order = 'DESC', bool $empty = false)
    {
        $res = $this->getSelect($order)
            ->orderBy($this->getPrimary().' '.$order)
            ->execute()
            ->setRowClass($this->rowClass)
            ->fetchPairs($this->getPrimary(), $this->labelCol);
        return ( $empty ? array_merge([ 'Bez omezení' ], $res) : $res );
    }

    /**
     * @param int $id
     * @return \Dibi\Row|false
     * @throws NotSupportedException
     * @throws \Dibi\Exception
     */
    public function getById(int $id)
    {
        return $this->getSelect()
                    ->where($this->getPrimary().' = '.$id)
                    ->execute()
                    ->setRowClass($this->rowClass)
                    ->fetch();
    }

    /**
     * @param string $cond
     * @param string $order
     * @param bool $empty
     * @return array
     * @throws Exception
     * @throws NotSupportedException
     */
    public function getByCond(string $cond, string $order = "ASC", bool $empty = false)
    {
        return $this->getSelect($order)
                    ->where($cond)
                    ->execute()
                    ->setRowClass($this->rowClass)
                    ->fetchAssoc($this->getPrimary());
    }

    /**
     * @param iterable $data
     * @return bool|int
     * @throws \Dibi\Exception
     */
    public function create(iterable $data)
    {
        $res = $this->db->insert($this->getTable(), $data)
            ->execute();
        try{
            return $res ? $this->db->getInsertId() : false;
        } catch (Exception $e){
            return false;
        }
    }

    /**
     * @param int $entityId
     * @param iterable $data
     * @return \Dibi\Result|int
     * @throws \Dibi\Exception
     */
    public function update(int $entityId, iterable $data){
        return $this->db->update($this->getTable(), $data)
                ->where($this->getPrimary().' = ?', $entityId)
                ->execute();
    }

    /**
     * @param int $id
     * @return \Dibi\Result|int
     * @throws \Dibi\Exception
     */
    public function delete(int $id)
    {
        $primary = $this->getPrimary();
        return $this->db->query("DELETE FROM `$this->table` WHERE `$primary` = ?", $id);
    }

    /**
     * @return \Dibi\Fluent
     */
    public function getSequenceVal()
    {
        return $this->db->select('AUTO_INCREMENT')
            ->from('INFORMATION_SCHEMA.TABLES')
            ->where('TABLE_SCHEMA = ? AND TABLE_NAME = ?', 'edomp', $this->table)
            ->fetchSingle();
    }

}
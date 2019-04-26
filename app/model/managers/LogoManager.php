<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 10.3.19
 * Time: 21:32
 */

namespace App\Model\Managers;

use App\Model\Entities\Logo;
use Dibi\NotSupportedException;

/**
 * Class LogoManager
 * @package app\model\managers
 */
class LogoManager extends BaseManager
{

    /**
     * @var string
     */
    protected $table = 'logo';

    /**
     * @var string
     */
    protected $rowClass = Logo::class;

    /**
     * @var string
     */
    protected $labelCol = "label";

    /**
     * @var array
     */
    protected $selectColumns = [
        "logo_id",
        "path",
        "label",
        "extension",
        "extension_tmp",
        "is_used",
        "created"
    ];

    /**
     * @param string $order
     * @param null $select
     * @param mixed ...$args
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
     * @param null $select
     * @param mixed ...$args
     * @return \Dibi\Fluent
     * @throws NotSupportedException
     */
    public function getDatagridSelect(string $order = 'DESC', $select = null, ...$args)
    {
        if(empty($this->selectColumns)){
            throw new NotSupportedException('Empty select column list. Did you specified [selectColumns] attribute?');
        }
        else{
            return $this->db->select($select !== null ? array_merge($this->selectColumns, (array)$select) : $this->selectColumns, ...$args)
                ->from($this->table)->orderBy($this->getPrimary().' '.$order)->where("path IS NOT NULL");
        }
    }

    /**
     * @return \Dibi\Result|int
     * @throws \Dibi\Exception
     */
    public function deleteEmpty()
    {
        return $this->db->delete($this->table)->where("path IS NULL")->execute();
    }

    /**
     * @param int $logoId
     * @return bool
     * @throws \Dibi\Exception
     */
    public function isInUsage(int $logoId): bool
    {
        $res = $this->db->select("*")
            ->from("test")
            ->where("logo_id = " . $logoId)
            ->execute()
            ->fetch();
        return $res ? true : false;
    }
}
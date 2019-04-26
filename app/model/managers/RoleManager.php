<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 18.4.19
 * Time: 10:28
 */

namespace App\Model\Managers;

use App\Model\Entities\Role;
use Nette\NotSupportedException;

/**
 * Class RoleManager
 * @package App\Model\Managers
 */
class RoleManager extends BaseManager
{
    /**
     * @var string
     */
    protected $table = "role";

    /**
     * @var string
     */
    protected $rowClass = Role::class;

    /**
     * @var array
     */
    protected $selectColumns = [
        'role_id',
        'label',
        'created'
    ];

    /**
     * @var string
     */
    protected $labelCol = 'label';

    public function getSelect(string $order = 'DESC', $select = null, ...$args)
    {
        if(empty($this->selectColumns)){
            throw new NotSupportedException('Empty select column list. Did you specified [selectColumns] attribute?');
        }
        else{
            return $this->db->select($select !== null ? array_merge($this->selectColumns, (array)$select) : $this->selectColumns, ...$args)
                ->from($this->table)
                ->where("role_id != " . $this->constHelper::ADMIN_ROLE)
                ->orderBy($this->getPrimary().' '.$order);
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 20.3.19
 * Time: 23:47
 */

namespace App\Model\Managers;

use App\Helpers\ConstHelper;
use App\Model\Entities\Category;
use App\Model\Entities\Role;
use App\Model\Entities\User;
use Dibi\Connection;
use Nette\NotSupportedException;
use Nette\Security\Passwords;

/**
 * Class UserManager
 * @package app\model\managers
 */
class UserManager extends BaseManager
{
    /**
     * @var string
     */
    protected $table = "user";

    /**
     * @var string
     */
    protected $roleTable = "role";

    /**
     * @var string
     */
    protected $roleRelTable = "user_role_rel";

    /**
     * @var string
     */
    protected $groupRelTable = "user_group_rel";

    /**
     * @var string
     */
    protected $rowClass = User::class;

    /**
     * @var string
     */
    protected $labelCol = 'login';

    /**
     * @var array
     */
    protected $selectColumns = [
        'user_id',
        'username',
        'password',
        'created'
    ];

    public function getSelect(string $order = 'DESC', $select = null, ...$args)
    {
        if(empty($this->selectColumns)){
            throw new NotSupportedException('Empty select column list. Did you specified [selectColumns] attribute?');
        }
        else{
            return $this->db->select($select !== null ? array_merge($this->selectColumns, (array)$select) : $this->selectColumns, ...$args)
                ->from($this->table)
                ->join($this->roleRelTable)
                ->using("(user_id)")
                ->where("role_id != " . $this->constHelper::ADMIN_ROLE)
                ->orderBy($this->getPrimary().' '.$order);
        }
    }

    public function getSelectAll(string $order = 'DESC', $select = null, ...$args)
    {
        if(empty($this->selectColumns)){
            throw new NotSupportedException('Empty select column list. Did you specified [selectColumns] attribute?');
        }
        else{
            return $this->db->select($select !== null ? array_merge($this->selectColumns, (array)$select) : $this->selectColumns, ...$args)
                ->from($this->table)
                ->orderBy($this->getPrimary().' '.$order);
        }
    }

    public function getForAuthentication(string $username)
    {
        return $this->getSelectAll()
            ->where("username = ?", $username)
            ->execute()
            ->setRowClass(User::class)
            ->fetch();
    }

    /**
     * @param iterable $data
     * @return bool|int|void
     * @throws \Dibi\Exception
     */
    public function create(iterable $data)
    {
        $this->db->insert($this->table, [
            "username" => $data->username,
            "password" => Passwords::hash($data->password)
        ])->execute();

        $userId = $this->db->getInsertId();

        $this->attachRoles($userId, $data->roles);
        $this->attachGroups($userId, $data->groups);
    }

    public function update(int $id, iterable $data)
    {
        $this->db->update($this->table, [
            "username" => $data->username
        ])
            ->where($this->getPrimary() . " = " . $id)
            ->execute();
        if($data->change_password){
            $this->db->update($this->table, [
                "password" => Passwords::hash($data->password)
            ])
                ->where($this->getPrimary() . " = " . $id)
                ->execute();
        }
        $this->detachRoles($id);
        $this->detachGroups($id);
        $this->attachRoles($id, $data->roles);
        $this->attachGroups($id, $data->groups);
    }

    /**
     * @param int $userId
     * @param iterable $roles
     * @throws \Dibi\Exception
     */
    public function attachRoles(int $userId, iterable $roles)
    {
        foreach($roles as $role){
            $this->db->insert($this->roleRelTable, [
                "user_id" => $userId,
                "role_id" => $role
            ])->execute();
        }
    }

    /**
     * @param int $userId
     * @return \Dibi\Result|int
     * @throws \Dibi\Exception
     */
    public function detachRoles(int $userId)
    {
        return $this->db->delete($this->roleRelTable)
            ->where($this->getPrimary() . " = " . $userId)
            ->execute();
    }

    /**
     * @param int $userId
     * @param iterable $groups
     * @throws \Dibi\Exception
     */
    public function attachGroups(int $userId, iterable $groups)
    {
        foreach($groups as $group){
            $this->db->insert($this->groupRelTable, [
                "user_id" => $userId,
                "group_id" => $group
            ])->execute();
        }
    }

    /**
     * @param int $userId
     * @return \Dibi\Result|int
     * @throws \Dibi\Exception
     */
    public function detachGroups(int $userId)
    {
        return $this->db->delete($this->groupRelTable)
            ->where($this->getPrimary() . " = " . $userId)
            ->execute();
    }

    /**
     * @param int $userId
     * @param bool $onlyId
     * @return array
     * @throws \Dibi\Exception
     */
    public function getRoles(int $userId, bool $onlyId = false): array
    {
        $rows = $this->db->select($this->roleTable . ".label, " . $this->roleTable . ".role_id")
            ->from($this->table)
            ->join($this->roleRelTable)
            ->on($this->table . "." . $this->getPrimary() . " = " . $this->roleRelTable . "." . $this->getPrimary())
            ->where($this->table . "." . $this->getPrimary() . "=" . $userId)
            ->join($this->roleTable)
            ->on($this->roleRelTable . ".role_id = " . $this->roleTable . ".role_id")
            ->execute()
            ->fetchPairs("role_id", "label");

        $res = [];

        if($onlyId){
            foreach($rows as $rowKey => $row)
                array_push($res, $rowKey);
            return $res;
        }

        return $rows;
    }

    /**
     * @param int $userId
     * @param bool $onlyId
     * @return array
     * @throws \Dibi\Exception
     */
    public function getGroupsIds(int $userId): array
    {
        $res = $this->db->select($this->groupRelTable . ".group_id")
            ->from($this->table)
            ->join($this->groupRelTable)
            ->using("(user_id)")
            ->where($this->table . "." . $this->getPrimary() . " = " . $userId)
            ->execute()
            ->fetchPairs();
        return $res;
    }

    /**
     * @param int $userId
     * @return array
     * @throws \Dibi\Exception
     */
    public function getCategories(int $userId = null): array
    {
        if($userId !== null){
            $query = $this->db->select("category_id, label")
            ->from($this->table)
            ->join("user_group_rel")
            ->using("(user_id)")
            ->join("group_category_rel")
            ->using("(group_id)")
            ->join("category")
            ->using("(category_id)")
            ->where($this->table . "." . $this->getPrimary() . " = " . $userId);
        }
        else{
            $query = $this->db->select("category_id, label")
                ->from("category");
        }

        $res = $query->execute()
            ->setRowClass(Category::class)
            ->fetchAssoc("category_id");

        return $res;
    }
}